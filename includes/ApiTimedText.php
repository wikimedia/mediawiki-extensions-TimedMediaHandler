<?php
/**
 * Copyright © 2015 Derk-Jan Hartman "hartman.wiki@gmail.com"
 * Updated 2017-2019 Brooke Vibber <bvibber@wikimedia.org>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 1.33
 */

namespace MediaWiki\TimedMediaHandler;

use LogicException;
use MediaWiki\Api\ApiBase;
use MediaWiki\Api\ApiFormatRaw;
use MediaWiki\Api\ApiMain;
use MediaWiki\Api\ApiResult;
use MediaWiki\Api\ApiUsageException;
use MediaWiki\Content\TextContent;
use MediaWiki\FileRepo\File\File;
use MediaWiki\FileRepo\RepoGroup;
use MediaWiki\Languages\LanguageNameUtils;
use MediaWiki\Page\WikiPage;
use MediaWiki\Page\WikiPageFactory;
use MediaWiki\TimedMediaHandler\Handlers\TextHandler\TextHandler;
use MediaWiki\Title\Title;
use Wikimedia\ObjectCache\WANObjectCache;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * Implements the timedtext module that outputs subtitle files
 * for consumption by <track> elements
 *
 * @ingroup API
 * @emits error.code timedtext-notfound, invalidlang, invalid-title
 */
class ApiTimedText extends ApiBase {
	private LanguageNameUtils $languageNameUtils;
	private RepoGroup $repoGroup;
	private WANObjectCache $cache;
	private WikiPageFactory $wikiPageFactory;

	/** @var int version of the cache format */
	private const CACHE_VERSION = 1;

	/** @var int default 24 hours */
	private const CACHE_TTL = 86400;

	public function __construct(
		ApiMain $main,
		string $action,
		LanguageNameUtils $languageNameUtils,
		RepoGroup $repoGroup,
		WANObjectCache $cache,
		WikiPageFactory $wikiPageFactory
	) {
		parent::__construct( $main, $action );
		$this->languageNameUtils = $languageNameUtils;
		$this->repoGroup = $repoGroup;
		$this->cache = $cache;
		$this->wikiPageFactory = $wikiPageFactory;
	}

	/**
	 * URLs to this API endpoint are intended to be created internally and provided
	 * opaquely in track lists. Not (yet) considered stable for external use.
	 */
	public function isInternal(): bool {
		return true;
	}

	/**
	 * This module uses a raw printer to directly output SRT, VTT or other subtitle formats
	 */
	public function getCustomPrinter(): ApiFormatRaw {
		$printer = new ApiFormatRaw( $this->getMain(), null );
		$printer->setFailWithHTTPError( true );
		return $printer;
	}

	public function execute(): void {
		$params = $this->extractRequestParams();

		if ( $params['lang'] === null ) {
			$langCode = false;
		} elseif ( !$this->languageNameUtils->isValidCode( $params['lang'] ) ) {
			$this->dieWithError(
				[ 'apierror-invalidlang', $this->encodeParamName( 'lang' ) ], 'invalidlang'
			);
		} else {
			$langCode = $params['lang'];
		}

		$page = $this->getTitleOrPageId( $params );
		if ( !$page->exists() ) {
			$this->dieWithError( 'apierror-missingtitle', 'timedtext-notfound' );
		}

		$ns = $page->getTitle()->getNamespace();
		if ( $ns !== NS_FILE ) {
			$this->dieWithError( 'apierror-filedoesnotexist', 'invalidtitle' );
		}
		$file = $this->repoGroup->findFile( $page->getTitle() );
		if ( !$file ) {
			$this->dieWithError( 'apierror-filedoesnotexist', 'timedtext-notfound' );
		}
		if ( !$file->isLocal() ) {
			$this->dieWithError( 'apierror-timedmedia-notlocal', 'timedtext-notlocal' );
		}

		// Find subtitle [content] that goes with file
		$page = $this->findTimedText( $file, $langCode, $params['trackformat'] );
		if ( !$page ) {
			$this->dieWithError( 'apierror-timedmedia-lang-notfound', 'timedtext-notfound' );
		}

		// TODO factor out. dupe with TimedTextPage.php
		$filename = $page->getTitle()->getDBkey();
		$titleParts = explode( '.', $filename );
		$timedTextExtension = array_pop( $titleParts );

		if ( $timedTextExtension !== $params['trackformat'] ) {
			$filename .= '.' . $params['trackformat'];
		}

		$rawTimedText = $this->convertTimedText(
			$timedTextExtension,
			$params['trackformat'],
			$page
		);

		// We want to cache our output
		$this->getMain()->setCacheMode( 'public' );
		if ( !$this->getMain()->getParameter( 'smaxage' ) ) {
			// cache at least 15 seconds.
			$this->getMain()->setCacheMaxAge( 15 );
		}

		if ( $params['trackformat'] === TimedTextPage::SRT_SUBTITLE_FORMAT ) {
			$mimeType = 'text/srt';
		} elseif ( $params['trackformat'] === TimedTextPage::VTT_SUBTITLE_FORMAT ) {
			$mimeType = 'text/vtt';
		} else {
			// Unreachable due to parameter validation,
			// unless someone adds a new format and forgets. :D
			throw new LogicException( 'Unsupported timedtext trackformat' );
		}

		$result = $this->getResult();
		$result->addValue( null, 'text', $rawTimedText, ApiResult::NO_SIZE_CHECK );
		$result->addValue( null, 'mime', $mimeType, ApiResult::NO_SIZE_CHECK );
		$result->addValue( null, 'filename', $filename, ApiResult::NO_SIZE_CHECK );
	}

	/**
	 * @throws ApiUsageException
	 */
	protected function findTimedText( File $file, string $langCode, string $preferredFormat ): ?WikiPage {
		// In future, add TimedTextPage::VTT_SUBTITLE_FORMAT as a supported input format as well.
		$sourceFormats = [ TimedTextPage::SRT_SUBTITLE_FORMAT ];

		$textHandler = new TextHandler( $file, $sourceFormats );
		$ns = $textHandler->getTimedTextNamespace();
		if ( !$ns ) {
			$this->dieWithError( 'apierror-timedmedia-no-timedtext-support', 'invalidconfig' );
		}

		foreach ( $sourceFormats as $format ) {
			$dbkey = "{$file->getTitle()->getDbKey()}.$langCode.$format";
			$page = $this->wikiPageFactory->newFromTitle( Title::makeTitle( $ns, $dbkey ) );
			if ( $page->exists() ) {
				if ( $page->isRedirect() ) {
					$title = $page->getRedirectTarget();
					if ( $title ) {
						$page = $this->wikiPageFactory->newFromTitle( $title );
					} else {
						return null;
					}
				}
				if ( $page->exists() ) {
					return $page;
				}
			}
		}
		return null;
	}

	/**
	 * Fetch and convert or normalize the given timetext source.
	 *
	 * Uses the main WAN cache storage for caching output; if cached
	 * data is available it will be used instead of fetching and
	 * converting the text anew.
	 *
	 * Cache items are auto-expired if the CACHE_VERSION constant
	 * changes or the page has been edited since last update.
	 *
	 * @param string $from one of TimedTextPage::SRT_SUBTITLE_FORMAT or TimedTextPage::VTT_SUBTITLE_FORMAT
	 * @param string $to one of TimedTextPage::VTT_SUBTITLE_FORMAT or TimedTextPage::SRT_SUBTITLE_FORMAT
	 * @param WikiPage $page the TimedText page being loaded
	 * @return string text of the output in desired format
	 */
	protected function convertTimedText( string $from, string $to, WikiPage $page ): string {
		$revId = $page->getLatest();
		$key = $this->cache->makeKey(
			'apitimedtext',
			self::CACHE_VERSION,
			$page->getTitle()->getDbKey(),
			$revId,
			$from,
			$to
		);
		return $this->cache->getWithSetCallback(
			$key,
			self::CACHE_TTL,
			static function ( $cached, &$ttl ) use ( $from, $to, $page ) {
				// TODO convert to contentmodel
				$content = $page->getContent();
				$rawTimedText = $content instanceof TextContent ? $content->getText() : '';
				return TextHandler::convertSubtitles(
					$from,
					$to,
					$rawTimedText
				);
			}
		);
	}

	public function getAllowedParams( int $flags = 0 ): array {
		$ret = [
			'title' => [
				ParamValidator::PARAM_TYPE => 'string',
			],
			'pageid' => [
				ParamValidator::PARAM_TYPE => 'integer'
			],
			'trackformat' => [
				ParamValidator::PARAM_TYPE => [
					TimedTextPage::SRT_SUBTITLE_FORMAT,
					TimedTextPage::VTT_SUBTITLE_FORMAT,
				],
				ParamValidator::PARAM_REQUIRED => true,
			],
			// Note this is the target language of the track to load,
			// and does not control things like the language of
			// error messages.
			//
			// Should not default to user language or anything, since
			// track URLs should be consistent and explicit.
			'lang' => [
				ParamValidator::PARAM_TYPE => 'string',
			],
		];
		return $ret;
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 * @return array of examples
	 */
	protected function getExamplesMessages(): array {
		return [
			'action=timedtext&title=File:Example.ogv&lang=de&trackformat=vtt'
				=> 'apihelp-timedtext-example-1',
		];
	}

	/** @inheritDoc */
	public function getHelpUrls(): string {
		return 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TimedMediaHandler';
	}
}
