<?php

// phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName

namespace MediaWiki\TimedMediaHandler;

use Article;
use DatabaseUpdater;
use DifferenceEngine;
use File;
use IContextSource;
use ImageHistoryList;
use ImagePage;
use LocalFile;
use MediaWiki\Diff\Hook\ArticleContentOnDiffHook;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\CanonicalNamespacesHook;
use MediaWiki\Hook\FileDeleteCompleteHook;
use MediaWiki\Hook\FileUploadHook;
use MediaWiki\Hook\ParserTestGlobalsHook;
use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use MediaWiki\Hook\TitleMoveHook;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;
use MediaWiki\MediaWikiServices;
use MediaWiki\Page\Hook\ArticleFromTitleHook;
use MediaWiki\Page\Hook\ArticlePurgeHook;
use MediaWiki\Page\Hook\ImageOpenShowImageInlineBeforeHook;
use MediaWiki\Page\Hook\ImagePageAfterImageLinksHook;
use MediaWiki\Page\Hook\ImagePageFileHistoryLineHook;
use MediaWiki\Page\Hook\RevisionFromEditCompleteHook;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\SpecialPage\Hook\WgQueryPagesHook;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\User\UserIdentity;
use OutputPage;
use Skin;
use SkinTemplate;
use Status;
use Title;
use User;
use WikiFilePage;
use WikiPage;

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
	FileUploadHook,
	ImageOpenShowImageInlineBeforeHook,
	ImagePageAfterImageLinksHook,
	ImagePageFileHistoryLineHook,
	LoadExtensionSchemaUpdatesHook,
	ParserTestGlobalsHook,
	RevisionFromEditCompleteHook,
	SkinTemplateNavigation__UniversalHook,
	TitleMoveHook,
	WgQueryPagesHook
{

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
		global $wgTimedTextNS;
		if ( !defined( 'NS_TIMEDTEXT' ) ) {
			define( 'NS_TIMEDTEXT', $wgTimedTextNS );
			define( 'NS_TIMEDTEXT_TALK', $wgTimedTextNS + 1 );
		}

		$list[NS_TIMEDTEXT] = 'TimedText';
		$list[NS_TIMEDTEXT_TALK] = 'TimedText_talk';
	}

	/**
	 * Register remaining TimedMediaHandler hooks right after initial setup
	 *
	 * TODO: This function shouldn't need to exist.
	 *
	 * @return bool
	 */
	public static function register() {
		global $wgJobTypesExcludedFromDefaultQueue,
		$wgExcludeFromThumbnailPurge,
		$wgFileExtensions, $wgTmhEnableMp4Uploads,
		$wgTmhFileExtensions;

		$wgFileExtensions = array_merge( $wgFileExtensions, $wgTmhFileExtensions );

		// Remove mp4 if not enabled:
		if ( $wgTmhEnableMp4Uploads === false ) {
			$index = array_search( 'mp4', $wgFileExtensions, true );
			if ( $index !== false ) {
				array_splice( $wgFileExtensions, $index, 1 );
			}
		}

		// Transcode jobs must be explicitly requested from the job queue:
		$wgJobTypesExcludedFromDefaultQueue[] = 'webVideoTranscode';

		// Exclude transcoded assets from normal thumbnail purging
		// ( a maintenance script could handle transcode asset purging)
		if ( isset( $wgExcludeFromThumbnailPurge ) ) {
			$wgExcludeFromThumbnailPurge = array_merge( $wgExcludeFromThumbnailPurge, $wgTmhFileExtensions );
			// Also add the .log file ( used in two pass encoding )
			// ( probably should move in-progress encodes out of web accessible directory )
			$wgExcludeFromThumbnailPurge[] = 'log';
		}

		// validate enabled transcodeset values
		WebVideoTranscode::validateTranscodeConfiguration();
		return true;
	}

	/**
	 * @param ImagePage $imagePage the imagepage that is being rendered
	 * @param OutputPage $output the output for this imagepage
	 * @return bool
	 */
	public function onImageOpenShowImageInlineBefore( $imagePage, $output ) {
		$file = $imagePage->getDisplayedFile();
		return self::onImagePageHooks( $file, $output );
	}

	/**
	 * @param ImageHistoryList $imageHistoryList that is being rendered
	 * @param File $file the (old) file added in this history entry
	 * @param string &$line the HTML of the history line
	 * @param string &$css the CSS class of the history line
	 * @return bool
	 */
	public function onImagePageFileHistoryLine( $imageHistoryList, $file, &$line, &$css ) {
		$out = $imageHistoryList->getContext()->getOutput();
		return self::onImagePageHooks( $file, $out );
	}

	/**
	 * @param File $file the file that is being rendered
	 * @param OutputPage $out the output to which this file is being rendered
	 * @return bool
	 */
	private static function onImagePageHooks( $file, $out ) {
		$handler = $file->getHandler();
		if ( $handler instanceof TimedMediaHandler ) {
			$out->addModuleStyles( 'ext.tmh.player.styles' );
			$out->addModules( 'ext.tmh.player' );
		}
		return true;
	}

	/**
	 * @param Title $title
	 * @param Article|null &$article
	 * @param IContextSource $context
	 * @return bool
	 */
	public function onArticleFromTitle( $title, &$article, $context ) {
		global $wgTimedTextNS;
		if ( $title->getNamespace() === $wgTimedTextNS ) {
			$article = new TimedTextPage( $title );
		}
		return true;
	}

	/**
	 * @param DifferenceEngine $diffEngine
	 * @param OutputPage $output
	 * @return bool
	 */
	public function onArticleContentOnDiff( $diffEngine, $output ) {
		global $wgTimedTextNS;
		if ( $output->getTitle()->getNamespace() === $wgTimedTextNS ) {
			$article = new TimedTextPage( $output->getTitle() );
			$article->renderOutput( $output );
			return false;
		}
		return true;
	}

	/**
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		if ( self::isTimedMediaHandlerTitle( $sktemplate->getTitle() ) ) {
			$ttTitle = Title::makeTitleSafe( NS_TIMEDTEXT, $sktemplate->getTitle()->getDBkey() );
			if ( !$ttTitle ) {
				return;
			}
			$links[ 'namespaces' ][ 'timedtext' ] =
				$sktemplate->tabAction( $ttTitle, 'timedtext', false, '', false );
		}
	}

	/**
	 * Wraps the isTranscodableFile function
	 * @param Title $title
	 * @return bool
	 */
	public static function isTranscodableTitle( $title ) {
		if ( $title->getNamespace() !== NS_FILE ) {
			return false;
		}
		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $title );
		return self::isTranscodableFile( $file );
	}

	/**
	 * Utility function to check if a given file can be "transcoded"
	 * @param File $file File object
	 * @return bool
	 */
	public static function isTranscodableFile( $file ) {
		global $wgEnableTranscode, $wgEnabledAudioTranscodeSet;

		// don't show the transcode table if transcode is disabled
		if ( !$wgEnableTranscode && !$wgEnabledAudioTranscodeSet ) {
			return false;
		}
		// Can't find file
		if ( !$file ) {
			return false;
		}
		// We can only transcode local files
		if ( !$file->isLocal() ) {
			return false;
		}

		$handler = $file->getHandler();
		// Not able to transcode files without handler
		if ( !$handler ) {
			return false;
		}
		$mediaType = $handler->getMetadataType( $file );
		// If ogg or webm format and not audio we can "transcode" this file
		$isAudio = $handler instanceof TimedMediaHandler && $handler->isAudio( $file );
		if ( ( $mediaType === 'webm' || $mediaType === 'ogg'
				|| $mediaType === 'mp4' || $mediaType === 'mpeg' )
			&& !$isAudio
		) {
			return true;
		}
		if ( $isAudio && count( $wgEnabledAudioTranscodeSet ) ) {
			return true;
		}
		return false;
	}

	/**
	 * @param Title $title
	 * @return bool
	 */
	public static function isTimedMediaHandlerTitle( $title ) {
		if ( !$title->inNamespace( NS_FILE ) ) {
			return false;
		}
		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $title );
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
	 * @return bool
	 */
	public function onImagePageAfterImageLinks( $imagePage, &$html ) {
		// load the file:
		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $imagePage->getTitle() );
		if ( self::isTranscodableFile( $file ) ) {
			$html .= TranscodeStatusTable::getHTML( $file, $imagePage->getContext() );
		}
		return true;
	}

	/**
	 * @param File $file LocalFile object
	 * @param bool $reupload
	 * @param bool $hasDescription
	 * @return bool
	 */
	public function onFileUpload( $file, $reupload, $hasDescription ) {
		// Check that the file is a transcodable asset:
		if ( $file && self::isTranscodableFile( $file ) ) {
			// Remove all the transcode files and db states for this asset
			WebVideoTranscode::removeTranscodes( $file );
			WebVideoTranscode::startJobQueue( $file );
		}
		return true;
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
	 * @return bool
	 */
	public function onTitleMove( Title $title, Title $newTitle, User $user, $reason, Status &$status ) {
		if ( self::isTranscodableTitle( $title ) ) {
			// Remove all the transcode files and db states for this asset
			// ( will be re-added the first time the asset is displayed with its new title )
			$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $title );
			WebVideoTranscode::removeTranscodes( $file );
		}
		return true;
	}

	/**
	 * Hook to FileDeleteComplete. Removes transcodes on delete.
	 * @param LocalFile $file
	 * @param string|null $oldimage
	 * @param WikiFilePage|null $article
	 * @param User $user
	 * @param string $reason
	 * @return bool
	 */
	public function onFileDeleteComplete( $file, $oldimage, $article, $user, $reason ) {
		if ( !$oldimage && self::isTranscodableFile( $file ) ) {
			WebVideoTranscode::removeTranscodes( $file );
		}
		return true;
	}

	/**
	 * If file gets reverted to a previous version, reset transcodes.
	 *
	 * @param WikiPage $wikiPage
	 * @param RevisionRecord $rev
	 * @param int $originalRevId
	 * @param UserIdentity $user
	 * @param string[] &$tags
	 *
	 * @return bool
	 */
	public function onRevisionFromEditComplete(
		$wikiPage, $rev, $originalRevId, $user, &$tags
	) {
		// Check if the article is a file and remove transcode files:
		if ( ( $originalRevId !== false ) && $wikiPage->getTitle()->getNamespace() === NS_FILE ) {
			$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $wikiPage->getTitle() );
			if ( self::isTranscodableFile( $file ) ) {
				WebVideoTranscode::removeTranscodes( $file );
				WebVideoTranscode::startJobQueue( $file );
			}
		}
		return true;
	}

	/**
	 * When a user asks for a purge, perhaps through our handy "update transcode status"
	 * link, make sure we've got the updated set of transcodes. This'll allow a user or
	 * automated process to see their status and reset them.
	 *
	 * @param WikiPage $wikiPage
	 * @return bool
	 */
	public function onArticlePurge( $wikiPage ) {
		if ( $wikiPage->getTitle()->getNamespace() === NS_FILE ) {
			$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $wikiPage->getTitle() );
			if ( self::isTranscodableFile( $file ) ) {
				WebVideoTranscode::cleanupTranscodes( $file );
			}
		}
		return true;
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
		global $wgTimedTextNS;

		$title = $out->getTitle();
		$namespace = $title->getNamespace();
		$addModules = false;

		if ( $namespace === NS_CATEGORY || $namespace === $wgTimedTextNS ) {
			$addModules = true;
		} elseif ( $title->isSpecialPage() ) {
			[ $name, ] = MediaWikiServices::getInstance()
				->getSpecialPageFactory()->resolveAlias( $title->getDBkey() );
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
	 * @param DatabaseUpdater $updater
	 * @return bool
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$dir = dirname( __DIR__ ) . '/sql/';
		$dbType = $updater->getDB()->getType();
		if ( $dbType === 'mysql' ) {
			$updater->addExtensionTable( 'transcode',
				$dir . 'tables-generated.sql'
			);
		} elseif ( $dbType === 'sqlite' ) {
			$updater->addExtensionTable( 'transcode',
				$dir . 'sqlite/tables-generated.sql'
			);
		} elseif ( $dbType === 'postgres' ) {
			$updater->addExtensionTable( 'transcode',
				$dir . 'postgres/tables-generated.sql'
			);
		}
		$dirPatch = $dbType === 'mysql' ? $dir : $dir . $dbType . '/';

		// 1.38
		$updater->modifyExtensionField(
			'transcode', 'transcode_time_error', $dirPatch . 'patch-transcode-transcode_timestamp.sql'
		);

		return true;
	}

	/**
	 * @param array &$qp
	 */
	public function onwgQueryPages( &$qp ) {
		$qp[] = [ SpecialOrphanedTimedText::class, 'OrphanedTimedText' ];
	}
}
