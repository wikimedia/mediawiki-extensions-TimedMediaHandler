<?php
/**
 * Created on Aug 17, 2015
 *
 * Copyright © 2015 Derk-Jan Hartman "hartman.wiki@gmail.com"
 * Updated 2017-2019 Brion Vibber <bvibber@wikimedia.org>
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

/**
 * Implements the timedtext module that outputs subtitle files
 * for consumption by <track> elements
 *
 * @ingroup API
 * @emits error.code timedtext-notfound, invalidlang, invalid-title
 */
class ApiTimedText extends ApiBase {

	const CACHE_VERSION = 1;
	const CACHE_TTL = 86400; // 24 hours

	/**
	 * URLs to this API endpoint are intended to be created internally and provided
	 * opaquely in track lists. Not (yet) considered stable for external use.
	 *
	 * @return bool
	 */
	public function isInternal() {
		return true;
	}

	/**
	 * This module uses a raw printer to directly output SRT, VTT or other subtitle formats
	 *
	 * @return ApiFormatRaw
	 */
	public function getCustomPrinter() {
		$printer = new ApiFormatRaw( $this->getMain(), null );
		$printer->setFailWithHTTPError( true );
		return $printer;
	}

	public function execute() {
		$params = $this->extractRequestParams();
		$result = $this->getResult();

		if ( $params['lang'] === null ) {
			$langCode = false;
		} elseif ( !Language::isValidCode( $params['lang'] ) ) {
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
		if ( $ns != NS_FILE ) {
			$this->dieWithError( 'apierror-filedoesnotexist', 'invalidtitle' );
		}
		$file = wfFindFile( $page->getTitle() );
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

		if ( $params['trackformat'] === 'srt' ) {
			$mimeType = 'text/srt';
		} elseif ( $params['trackformat'] === 'vtt' ) {
			$mimeType = 'text/vtt';
		} else {
			throw new MWException( 'Unsupported timedtext trackformat' );
		}

		$result = $this->getResult();
		$result->addValue( null, 'text', $rawTimedText, ApiResult::NO_SIZE_CHECK );
		$result->addValue( null, 'mime', $mimeType, ApiResult::NO_SIZE_CHECK );
		$result->addValue( null, 'filename', $filename, ApiResult::NO_SIZE_CHECK );
	}

	protected function findTimedText( $file, $langCode, $preferredFormat ) {
		// In future, add 'vtt' as a supported input format as well.
		$sourceFormats = [ 'srt' ];

		$textHandler = new TextHandler( $file, $sourceFormats );
		$ns = $textHandler->getTimedTextNamespace();
		if ( !$ns ) {
			$this->dieWithError( 'apierror-timedmedia-no-timedtext-support', 'invalidconfig' );
		}

		foreach ( $sourceFormats as $format ) {
			$dbkey = "{$file->getTitle()->getDbKey()}.{$langCode}.{$format}";
			$page = WikiPage::factory( Title::makeTitle( $ns, $dbkey ) );
			if ( $page->exists() ) {
				if ( $page->isRedirect() ) {
					$title = $page->getRedirectTarget();
					if ( $title ) {
						$page = WikiPage::factory( $title );
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
	 * Uses the parser cache storage for caching output; if cached
	 * data is available it will be used instead of fetching and
	 * converting the text anew.
	 *
	 * Cache items are auto-expired if the CACHE_VERSION constant
	 * changes or the page has been edited since last update.
	 *
	 * @param string $from one of 'srt' or 'vtt'
	 * @param string $to one of 'vtt' or 'srt'
	 * @param WikiPage $page the TimedText page being loaded
	 * @return string text of the output in desired format
	 */
	protected function convertTimedText( $from, $to, $page ) {
		global $wgParserCacheType;

		$cache = ObjectCache::getInstance( $wgParserCacheType );
		$key = $cache->makeKey(
			'apitimedtext',
			$page->getTitle()->getDbKey(),
			$from,
			$to
		);
		$cached = $cache->get( $key );

		$revId = $page->getLatest();
		if ( $cached === false
			|| $cached['cache_version'] !== self::CACHE_VERSION
			|| $cached['rev_id'] !== $revId
		) {
			// TODO convert to contentmodel
			$rawTimedText = $page->getContent()->getNativeData();
			$output = TextHandler::convertSubtitles(
				$from,
				$to,
				$rawTimedText
			);
			$cached = [
				'cache_version' => self::CACHE_VERSION,
				'rev_id' => $revId,
				'output' => $output,
			];
			$cache->set( $key, $cached, self::CACHE_TTL );
		}
		return $cached['output'];
	}

	public function getAllowedParams( $flags = 0 ) {
		$ret = [
			'title' => [
				ApiBase::PARAM_TYPE => 'string',
			],
			'pageid' => [
				ApiBase::PARAM_TYPE => 'integer'
			],
			'trackformat' => [
				ApiBase::PARAM_TYPE => [ 'srt', 'vtt' ],
			],
			// Note this is the target language of the track to load,
			// and does not control things like the language of
			// error messages.
			//
			// Should not default to user language or anything, since
			// track URLs should be consistent and explicit.
			'lang' => [
				ApiBase::PARAM_TYPE => 'string',
			],
		];
		return $ret;
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 * @return array of examples
	 */
	protected function getExamplesMessages() {
		return [
			'action=timedtext&title=File:Example.ogv&lang=de&trackformat=vtt'
				=> 'apihelp-timedtext-example-1',
		];
	}

	public function getHelpUrls() {
		return 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TimedMediaHandler';
	}
}
