<?php
/**
 * TimedText page display the current video with subtitles to the right.
 *
 * Future features for this page
 * @todo add srt download links
 * @todo parse and validate srt files
 * @todo link-in or include the universal subtitles editor
 */

namespace MediaWiki\TimedMediaHandler;

use Article;
use File;
use MediaWiki\Content\TextContent;
use MediaWiki\Html\Html;
use MediaWiki\HTMLForm\HTMLForm;
use MediaWiki\Language\LanguageCode;
use MediaWiki\Languages\LanguageNameUtils;
use MediaWiki\MediaWikiServices;
use MediaWiki\Output\OutputPage;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Status\Status;
use MediaWiki\Title\Title;
use StatusValue;

class TimedTextPage extends Article {

	/** @var int The width of the video plane. Must match the CSS */
	private static $videoWidth = 400;

	// WebVTT
	public const VTT_SUBTITLE_FORMAT = 'vtt';

	// SubRIP (SRT)
	public const SRT_SUBTITLE_FORMAT = 'srt';

	/** @var string[] */
	private static $knownTimedTextExtensions = [
		self::SRT_SUBTITLE_FORMAT,
		self::VTT_SUBTITLE_FORMAT,
	];

	/**
	 * @var LanguageNameUtils
	 */
	private $languageNameUtils;

	/**
	 * The file associated with this subtitle page
	 * @var File|null
	 */
	private $correspondingFile;

	/**
	 * @var Title|null
	 */
	private $correspondingFileTitle;

	/**
	 * The TimedText format extracted from this page's title
	 * @var null|string
	 */
	private $timedTextFormat;

	/**
	 * The language key extracted from this page's title
	 * @var null|string
	 */
	private $languageKey;

	/**
	 * Status result of the view rendering
	 *
	 * @var StatusValue
	 */
	private StatusValue $renderStatus;

	/** @inheritDoc */
	public function __construct( Title $title, $oldId = null ) {
		parent::__construct( $title, $oldId );
		$services = MediaWikiServices::getInstance();
		$this->languageNameUtils = $services->getLanguageNameUtils();

		// We parse the title of this page to find the corresponding file's title
		// in the File namespace, the language of the subtitles and the format.
		$titleParts = explode( '.', $title->getDBkey() );
		$timedTextExtension = array_slice( $titleParts, -1, 1 )[0] ?? null;

		if ( in_array( $timedTextExtension, static::$knownTimedTextExtensions, true ) ) {
			// File name with text extension ( from remaining parts of title )
			// i.e TimedText:myfile.ogg.en.srt
			$this->timedTextFormat = $timedTextExtension;
			$this->languageKey = array_slice( $titleParts, -2, 1 )[0] ?? null;
			$titleName = implode( '.', array_slice( $titleParts, 0, -2 ) );
			$this->correspondingFileTitle = Title::newFromText( $titleName, NS_FILE );
		} else {
			// File name without text extension:
			// i.e TimedText:myfile.ogg
			$this->correspondingFileTitle = Title::newFromText( $this->getTitle()->getDBkey(), NS_FILE );
			$this->timedTextFormat = null;
			$this->languageKey = null;
		}
		$this->renderStatus = Status::newGood();
	}

	public function view(): void {
		$request = $this->getContext()->getRequest();
		$out = $this->getContext()->getOutput();
		$diff = $request->getVal( 'diff' );
		// getOldID has side effects
		$oldid = $this->getOldID();

		if ( $this->mRedirectUrl || $diff !== null || $this->getTitle()->getNamespace() !== NS_TIMEDTEXT ) {
			parent::view();
			return;
		}

		// Article flag is required for some editors, and other features (T307218).
		$out->setArticleFlag( true );

		$this->showRedirectedFromHeader();
		$this->showNamespaceHeader();

		$this->renderOutput( $out );
	}

	/**
	 * Render TimedText to given output
	 *
	 * This function is used for views and diff views
	 * It is somewhat special as it renders two separate units of content,
	 * the timedtext and the corresponding file for that timedtext
	 *
	 * @param OutputPage $out
	 */
	public function renderOutput( OutputPage $out ): void {
		$this->renderStatus = Status::newGood();

		// Check for the new/edit page title format
		// i.e TimedText:myfile.ogg
		if ( !$this->isActualTimedTextTitle() ) {
			$this->doRedirectToPageForm( $out );
			return;
		}
		// We want to render the contents of the page

		// Look up the language name for the language that these subtitles use:
		$language = $out->getLanguage()->getCode();
		$languages = $this->languageNameUtils->getLanguageNames( $language, LanguageNameUtils::ALL );
		// @phan-suppress-next-line PhanTypeMismatchDimFetchNullable
		$languageName = $languages[ $this->getLanguageKey() ] ?? $this->getLanguageKey();

		// Set title of the page
		$message = $this->getPage()->exists() ?
			'timedmedia-timedtext-title-edit-subtitles' :
			'timedmedia-timedtext-title-create-subtitles';
		$out->setPageTitleMsg( wfMessage( $message, $languageName, $this->getCorrespondingFileTitle() ) );

		// Attempt to render the content
		$fileHtml = $this->getFileHTML();
		$timedTextHtml = $this->getTimedTextHTML( $out, $languageName );

		// Generate the page
		$out->addHTML( $this->getErrorsAndWarnings( $this->renderStatus ) );
		$out->addModuleStyles( [ 'ext.tmh.timedtextpage.styles' ] );

		if ( !$this->renderStatus->isOK() ) {
			return;
		}

		// Layout
		$out->addHtml(
			Html::rawElement( 'div', [ 'class' => 'mw-timedtextpage-layout' ],
				Html::rawElement( 'div', [ 'class' => 'mw-timedtextpage-video' ],
					$fileHtml
				) .
				Html::rawElement(
					'div',
					[ 'class' => 'mw-timedtextpage-tt' ],
					$timedTextHtml
				)
			)
		);
	}

	/**
	 * We show this form if a valid local file exists for this title.
	 * i.e TimedText:myfile.ogg
	 *
	 * @return void
	 * @throws \MWException
	 */
	private function doRedirectToPageForm( OutputPage $out ): void {
		$context = $out->getContext();
		$lang = $context->getLanguage();
		$file = $this->getCorrespondingFile();

		// Set the page title:
		$out->setPageTitleMsg( wfMessage( 'timedmedia-subtitle-new' ) );

		if ( $file && !$file->isLocal() ) {
			// Corresponding file is hosted on remote repo.
			// People aren't really supposed to be here, so link to foreign repo
			// TODO these two messages should be combined into a single one
			$out->addHTML( Html::warningBox(
				wfMessage( 'timedmedia-subtitle-remote',
					$file->getRepo()->getDisplayName() )->parse(),
				'' )
			);
			$out->addHTML( Html::warningBox(
				wfMessage( 'timedmedia-subtitle-remote-link',
					$file->getDescriptionUrl(),
					$file->getRepo()->getDisplayName() )->parse(),
				'' )
			);
			return;
		}

		if ( !$file ) {
			$this->renderStatus->warning( 'timedmedia-subtitle-no-video' );
		}

		$languages = $this->languageNameUtils->getLanguageNames(
			LanguageNameUtils::AUTONYMS,
			LanguageNameUtils::SUPPORTED
		);
		$options = [];
		foreach ( $languages as $code => $name ) {
			$display = LanguageCode::bcp47( $code ) . ' - ' . $name;
			$options[$display] = $code;
		}

		$formDescriptor = [
			'errorsandwarnings' => [
				'type' => 'info',
				'raw' => true,
				'default' => $this->getErrorsAndWarnings( $this->renderStatus )
			],
			'lang' => [
				'label-message' => 'timedmedia-subtitle-new-desc',
				'required' => true,
				'type' => 'select',
				'options' => $options,
				'default' => $lang->getCode(),
			],
		];

		$out->enableOOUI();
		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $context );
		$htmlForm
			->setMethod( 'post' )
			->setSubmitTextMsg( 'timedmedia-subtitle-new-go' )
			->prepareForm()
			->setSubmitCallback( [ $this, 'onSubmit' ] )
			->show();
	}

	/** @inheritDoc */
	public function onSubmit( array $data ): bool {
		if ( !empty( $data['lang'] ) ) {
			$output = $this->getContext()->getOutput();
			$target = $output->getTitle() . '.' . $data['lang'] . '.' . self::SRT_SUBTITLE_FORMAT;
			$targetFullUrl = $output->getTitle()->getFullUrl() . '.' . $data['lang'] . '.' . self::SRT_SUBTITLE_FORMAT;
			if ( Title::newFromText( $target )->exists() ) {
				$output->redirect( $targetFullUrl );
			} else {
				$output->redirect( $targetFullUrl . '?action=edit' );
			}
			return true;
		}
		return false;
	}

	private function getErrorsAndWarnings( StatusValue $status ): string {
		$results = [];
		foreach ( $status->getErrorsByType( 'error' ) as $error ) {
			$results[] = Html::errorBox( wfMessage( $error[ 'message' ], $error[ 'params' ] )->parse() );
		}
		foreach ( $status->getErrorsByType( 'warning' ) as $error ) {
			$results[] = Html::warningBox( wfMessage( $error[ 'message' ], $error[ 'params' ] )->parse() );
		}
		return implode( "\n", $results );
	}

	/**
	 * Gets the video HTML ( with the current language set as default )
	 *
	 * @return string
	 */
	private function getFileHTML(): string {
		// Get the video embed:
		$file = $this->getCorrespondingFile();
		if ( !$file ) {
			// TODO fix this message to be video AND audio
			$this->renderStatus->error( 'timedmedia-subtitle-no-video' );
			return '';
		}
		if ( !$file->isLocal() ) {
			// File is hosted on remote repo, Add a short description and link to foreign repo
			// TODO these two messages should be combined into a single one
			$this->renderStatus->warning( 'timedmedia-subtitle-remote',
				$file->getRepo()->getDisplayName() );
			$this->renderStatus->warning( 'timedmedia-subtitle-remote-link',
				$file->getDescriptionUrl(), $file->getRepo()->getDisplayName() );
			return '';
		}
		if ( $this->getTitle()->isRedirect() ) {
			return '';
		}

		return $file->transform( [
			'width' => self::$videoWidth
		] )->toHtml();
	}

	/**
	 * Gets an HTML representation of the Timed Text
	 *
	 * @param OutputPage $out
	 * @param string $languageName
	 * @return string
	 */
	private function getTimedTextHTML( OutputPage $out, string $languageName ) {
		$file = $this->getCorrespondingFile();
		if ( !$this->getPage()->exists() ) {
			if ( $file && $file->isLocal() ) {
				$this->renderStatus->warning( 'timedmedia-subtitle-no-subtitles', $languageName );
			}
			return '';
		}
		if ( $file && !$file->isLocal() ) {
			// There are local subtitles for remote file, which doesn't work
			$this->renderStatus->error( 'timedmedia-subtitle-no-video' );
		}
		$oldid = $this->getOldID();
		// Are we looking at an old revision
		if ( $oldid && $this->fetchRevisionRecord() ) {
			$out->setRevisionId( $this->getRevIdFetched() );
			$this->setOldSubtitle( $oldid );

			if ( !$this->showDeletedRevisionHeader() ) {
				wfDebug( __METHOD__ . ": cannot view deleted revision\n" );
			}
		}
		$revision = $this->fetchRevisionRecord();
		if ( !$revision ) {
			$this->renderStatus->fatal( 'noarticletext' );
			return '';
		}

		$content = $revision->getContent(
			SlotRecord::MAIN,
			RevisionRecord::FOR_THIS_USER,
			$this->getContext()->getUser()
		);
		if ( !$content ) {
			$this->renderStatus->fatal( 'rev-deleted-text-permission', $languageName );
			return '';
		}

		if ( !$oldid ) {
			// Set wgRevision at the end from what we actually fetched.
			$out->setRevisionId( $this->getRevIdFetched() );
		}
		return Html::element(
			'pre',
			[],
			( $content instanceof TextContent ) ? $content->getText() : ''
		);
	}

	/**
	 * Retrieve the file associated with this TimedText page
	 * Returns null if no file is associated or no file exists,
	 * either locally or on a remote server or if it is not a TimedMediaHandler file
	 *
	 * @return File|null
	 */
	public function getCorrespondingFile(): ?File {
		if ( $this->correspondingFile ) {
			return $this->correspondingFile;
		}

		$repoGroup = MediaWikiServices::getInstance()->getRepoGroup();
		$fileTitle = $this->getCorrespondingFileTitle();
		if ( !$fileTitle ) {
			return null;
		}
		$file = $repoGroup->findFile( $fileTitle, [ 'ignoreRedirect' => true ] );

		if ( $file && $file->exists() && $file->getHandler() instanceof TimedMediaHandler ) {
			$this->correspondingFile = $file;
			return $this->correspondingFile;
		}
		return null;
	}

	/**
	 * The media file title that should belong to this TimedText page
	 *
	 * The title doesn't necessarily have to exist
	 * @return Title|null
	 */
	public function getCorrespondingFileTitle(): ?Title {
		return $this->correspondingFileTitle;
	}

	/**
	 * Returns the extension/timedtext type, based on the page title
	 * @return string|null
	 */
	public function getTimedTextFormat(): ?string {
		return $this->timedTextFormat;
	}

	/**
	 * Only pages that end with .languageKey.srt
	 * are known allowed names for TimedText pages.
	 *
	 * @return bool
	 */
	public function isActualTimedTextTitle(): bool {
		return (bool)$this->getTimedTextFormat();
	}

	/**
	 * Returns the language key code from the page title, if present
	 * @return string|null
	 */
	public function getLanguageKey(): ?string {
		return $this->languageKey;
	}

}
