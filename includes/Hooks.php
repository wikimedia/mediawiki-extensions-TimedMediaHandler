<?php

// phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName

namespace MediaWiki\TimedMediaHandler;

use DifferenceEngine;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Diff\Hook\ArticleContentOnDiffHook;
use MediaWiki\FileRepo\File\File;
use MediaWiki\FileRepo\File\LocalFile;
use MediaWiki\FileRepo\RepoGroup;
use MediaWiki\Hook\CanonicalNamespacesHook;
use MediaWiki\Hook\FileDeleteCompleteHook;
use MediaWiki\Hook\FileUndeleteCompleteHook;
use MediaWiki\Hook\FileUploadHook;
use MediaWiki\Hook\PageMoveCompleteHook;
use MediaWiki\Hook\ParserTestGlobalsHook;
use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use MediaWiki\Hook\TitleMoveHook;
use MediaWiki\Html\Html;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\Output\Hook\BeforePageDisplayHook;
use MediaWiki\Output\OutputPage;
use MediaWiki\Page\Article;
use MediaWiki\Page\Hook\ArticleFromTitleHook;
use MediaWiki\Page\Hook\ArticlePurgeHook;
use MediaWiki\Page\Hook\ImageOpenShowImageInlineBeforeHook;
use MediaWiki\Page\Hook\ImagePageAfterImageLinksHook;
use MediaWiki\Page\Hook\ImagePageFileHistoryLineHook;
use MediaWiki\Page\ImageHistoryList;
use MediaWiki\Page\ImagePage;
use MediaWiki\Page\WikiFilePage;
use MediaWiki\Page\WikiPage;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Skin\Skin;
use MediaWiki\Skin\SkinTemplate;
use MediaWiki\SpecialPage\Hook\WgQueryPagesHook;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\Status\Status;
use MediaWiki\TimedMediaHandler\Handlers\TextHandler\TextHandler;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use MediaWiki\User\UserIdentity;

/**
 * Hooks for TimedMediaHandler extension
 *
 * @file
 * @ingroup Extensions
 */
class Hooks implements
	ArticleContentOnDiffHook,
	ArticleFromTitleHook,
	ArticlePurgeHook,
	BeforePageDisplayHook,
	CanonicalNamespacesHook,
	FileDeleteCompleteHook,
	FileUndeleteCompleteHook,
	FileUploadHook,
	ImageOpenShowImageInlineBeforeHook,
	ImagePageAfterImageLinksHook,
	ImagePageFileHistoryLineHook,
	PageMoveCompleteHook,
	ParserTestGlobalsHook,
	SkinTemplateNavigation__UniversalHook,
	TitleMoveHook,
	WgQueryPagesHook
{

	private readonly TranscodableChecker $transcodableChecker;

	public function __construct(
		private readonly Config $config,
		private readonly LinkRenderer $linkRenderer,
		private readonly RepoGroup $repoGroup,
		private readonly SpecialPageFactory $specialPageFactory,
	) {
		$this->transcodableChecker = new TranscodableChecker(
			$config,
			$repoGroup
		);
	}

	/**
	 * Register TimedMediaHandler namespace IDs
	 *
	 * This way if you set a variable like $wgTimedTextNS in LocalSettings.php
	 * after you include TimedMediaHandler we can still read the variable values
	 *
	 * These are configurable due to Commons history: T123823
	 * These need to be before registerhooks due to: T123695
	 *
	 * @param array &$list
	 */
	public function onCanonicalNamespaces( &$list ) {
		if ( !defined( 'NS_TIMEDTEXT' ) ) {
			$timedTextNS = $this->config->get( 'TimedTextNS' );
			define( 'NS_TIMEDTEXT', $timedTextNS );
			define( 'NS_TIMEDTEXT_TALK', $timedTextNS + 1 );
		}

		$list[NS_TIMEDTEXT] = 'TimedText';
		$list[NS_TIMEDTEXT_TALK] = 'TimedText_talk';
	}

	/**
	 * @param ImagePage $imagePage the imagepage that is being rendered
	 * @param OutputPage $output the output for this imagepage
	 */
	public function onImageOpenShowImageInlineBefore( $imagePage, $output ): void {
		$file = $imagePage->getDisplayedFile();
		self::onImagePageHooks( $file, $output );
	}

	/**
	 * @param ImageHistoryList $imageHistoryList that is being rendered
	 * @param File $file the (old) file added in this history entry
	 * @param string &$line the HTML of the history line
	 * @param string &$css the CSS class of the history line
	 */
	public function onImagePageFileHistoryLine( $imageHistoryList, $file, &$line, &$css ): void {
		$out = $imageHistoryList->getContext()->getOutput();
		self::onImagePageHooks( $file, $out );
	}

	/**
	 * @param File $file the file that is being rendered
	 * @param OutputPage $out the output to which this file is being rendered
	 */
	private static function onImagePageHooks( File $file, $out ) {
		$handler = $file->getHandler();
		if ( $handler instanceof TimedMediaHandler ) {
			$out->addModuleStyles( 'ext.tmh.player.styles' );
			$out->addModules( 'ext.tmh.player' );
		}
	}

	/**
	 * @param Title $title
	 * @param Article|null &$article
	 * @param IContextSource $context
	 */
	public function onArticleFromTitle( $title, &$article, $context ): void {
		if ( $title->getNamespace() === $this->config->get( 'TimedTextNS' ) ) {
			$article = new TimedTextPage( $title );
		}
	}

	/**
	 * @param DifferenceEngine $diffEngine
	 * @param OutputPage $output
	 * @return bool|void True or no return value to continue or false to abort
	 */
	public function onArticleContentOnDiff( $diffEngine, $output ) {
		if ( $output->getTitle()->getNamespace() === $this->config->get( 'TimedTextNS' ) ) {
			$article = new TimedTextPage( $output->getTitle(), $diffEngine->getNewId() );
			$article->renderOutput( $output );
			return false;
		}
	}

	/**
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		if ( $this->isTimedMediaHandlerTitle( $sktemplate->getTitle() ) ) {
			$ttTitle = Title::makeTitleSafe( NS_TIMEDTEXT, $sktemplate->getTitle()->getDBkey() );
			if ( !$ttTitle ) {
				return;
			}
			$tab = $sktemplate->tabAction( $ttTitle, 'nstab-timedtext', false, '', false );

			// Lookup if we have any corresponding timed text available already
			$file = $this->repoGroup->findFile( $sktemplate->getTitle(), [ 'ignoreRedirect' => true ] );
			$textHandler = new TextHandler( $file, [ TimedTextPage::VTT_SUBTITLE_FORMAT ] );
			$ttExists = count( $textHandler->getTracks() ) > 0;
			$tab[ 'exists' ] = $ttExists;
			if ( !$ttExists ) {
				Html::addClass( $tab[ 'class' ], 'new' );
			}
			$links[ 'namespaces' ][ 'timedtext' ] = $tab;
			return;
		}
		if ( $sktemplate->getTitle()->getNamespace() === $this->config->get( 'TimedTextNS' ) ) {
			$fileTitle = ( new TimedTextPage( $sktemplate->getTitle() ) )->getCorrespondingFileTitle();
			if ( !$fileTitle ) {
				return;
			}

			$links['namespaces']['file'] =
				$sktemplate->tabAction( $fileTitle, 'nstab-image', false, '', true );
		}
	}

	/**
	 * @param Title $title
	 */
	private function isTimedMediaHandlerTitle( $title ): bool {
		if ( !$title->inNamespace( NS_FILE ) ) {
			return false;
		}
		$file = $this->repoGroup->findFile( $title, [ 'ignoreRedirect' => true ] );
		// Can't find file
		if ( !$file ) {
			return false;
		}
		$handler = $file->getHandler();
		if ( !$handler ) {
			return false;
		}
		return $handler instanceof TimedMediaHandler;
	}

	/**
	 * @param Article $imagePage
	 * @param string &$html
	 */
	public function onImagePageAfterImageLinks( $imagePage, &$html ): void {
		// load the file:
		$file = $this->repoGroup->findFile( $imagePage->getTitle(), [ 'ignoreRedirect' => true ] );
		if ( $this->transcodableChecker->isTranscodableFile( $file ) ) {
			$transcodeStatusTable = new TranscodeStatusTable(
				$imagePage->getContext(),
				$this->linkRenderer
			);
			$html .= $transcodeStatusTable->getHTML( $file );
		}
	}

	/**
	 * @param File $file LocalFile object
	 * @param bool $reupload
	 * @param bool $hasDescription
	 */
	public function onFileUpload( $file, $reupload, $hasDescription ): void {
		// Check that the file is a transcodable asset:
		if ( $this->transcodableChecker->isTranscodableFile( $file ) ) {
			// Remove all the transcode files and db states for this asset
			WebVideoTranscode::removeTranscodes( $file );
			WebVideoTranscode::startJobQueue( $file );
		}
	}

	/**
	 * Handle moved titles
	 *
	 * For now we just remove all the derivatives for the oldTitle. In the future we could
	 * look at moving the files, but right now thumbs are not moved, so I don't want to be
	 * inconsistent.
	 * @param Title $title
	 * @param Title $newTitle
	 * @param User $user
	 * @param string $reason
	 * @param Status &$status
	 */
	public function onTitleMove( Title $title, Title $newTitle, User $user, $reason, Status &$status ): void {
		if ( $this->transcodableChecker->isTranscodableTitle( $title ) ) {
			// Remove all the transcode files and db states for this asset
			// Will be re-added after the file has moved
			$file = $this->repoGroup->findFile( $title, [ 'ignoreRedirect' => true ] );
			WebVideoTranscode::removeTranscodes( $file );
		}
	}

	/**
	 * Hook to PageMoveComplete. Add transcode jobs for new file name
	 * @param LinkTarget $old Old title
	 * @param LinkTarget $new New title
	 * @param UserIdentity $user User who did the move
	 * @param int $pageid Database ID of the page that's been moved
	 * @param int $redirid Database ID of the created redirect
	 * @param string $reason Reason for the move
	 * @param RevisionRecord $revision RevisionRecord created by the move
	 */
	public function onPageMoveComplete( $old, $new, $user, $pageid, $redirid, $reason, $revision ): void {
		if ( $this->transcodableChecker->isTranscodableTitle( $new ) ) {
			$newFile = $this->repoGroup->findFile( $new, [ 'ignoreRedirect' => true, 'latest' => true ] );
			WebVideoTranscode::startJobQueue( $newFile );
		}
	}

	/**
	 * Hook to FileDeleteComplete. Removes transcodes on delete.
	 * @param LocalFile $file
	 * @param string|null $oldimage
	 * @param WikiFilePage|null $article
	 * @param User $user
	 * @param string $reason
	 */
	public function onFileDeleteComplete( $file, $oldimage, $article, $user, $reason ): void {
		if ( !$oldimage && $this->transcodableChecker->isTranscodableFile( $file ) ) {
			WebVideoTranscode::removeTranscodes( $file );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function onFileUndeleteComplete( $title, $fileVersions, $user, $reason ) {
		$file = $this->repoGroup->findFile( $title, [ 'ignoreRedirect' => true, 'latest' => true ] );
		if ( $file && $this->transcodableChecker->isTranscodableFile( $file ) ) {
			WebVideoTranscode::removeTranscodes( $file );
			WebVideoTranscode::startJobQueue( $file );
		}
	}

	/**
	 * When a user asks for a purge, perhaps through our handy "update transcode status"
	 * link, make sure we've got the updated set of transcodes. This'll allow a user or
	 * automated process to see their status and reset them.
	 *
	 * @param WikiPage $wikiPage
	 */
	public function onArticlePurge( $wikiPage ): void {
		if ( $wikiPage->getTitle()->getNamespace() === NS_FILE ) {
			$file = $this->repoGroup->findFile( $wikiPage->getTitle(), [ 'ignoreRedirect' => true ] );
			if ( $this->transcodableChecker->isTranscodableFile( $file ) ) {
				WebVideoTranscode::cleanupTranscodes( $file );
			}
		}
	}

	/**
	 * @param array &$globals
	 */
	public function onParserTestGlobals( &$globals ) {
		// reset player serial so that parser tests are not order-dependent
		TimedMediaTransformOutput::resetSerialForTest();

		$globals['wgEnableTranscode'] = false;
		$globals['wgFFmpegLocation'] = '/usr/bin/ffmpeg';
	}

	/**
	 * Add JavaScript and CSS for special pages that may include timed media
	 * but which will not fire the parser hook.
	 *
	 * FIXME: There ought to be a better interface for determining whether the
	 * page is liable to contain timed media.
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		$title = $out->getTitle();
		$namespace = $title->getNamespace();
		$addModules = false;

		if ( $namespace === NS_CATEGORY || $namespace === $this->config->get( 'TimedTextNS' ) ) {
			$addModules = true;
		} elseif ( $title->isSpecialPage() ) {
			[ $name, ] = $this->specialPageFactory->resolveAlias( $title->getDBkey() );
			if ( $name !== null && (
					$name === 'Search' ||
					$name === 'GlobalUsage' ||
					$name === 'Upload' ||
					stripos( $name, 'file' ) !== false ||
					stripos( $name, 'image' ) !== false
				)
			) {
				$addModules = true;
			}
		}

		if ( $addModules ) {
			$out->addModuleStyles( 'ext.tmh.player.styles' );
			$out->addModules( 'ext.tmh.player' );
		}
	}

	/**
	 * @param array &$qp
	 */
	public function onwgQueryPages( &$qp ) {
		$qp[] = [ SpecialOrphanedTimedText::class, 'OrphanedTimedText' ];
	}
}
