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

use MediaWiki\Content\TextContent;
use MediaWiki\Exception\MWException;
use MediaWiki\FileRepo\File\File;
use MediaWiki\Html\Html;
use MediaWiki\HTMLForm\HTMLForm;
use MediaWiki\Language\LanguageCode;
use MediaWiki\Language\LanguageNameUtils;
use MediaWiki\MediaWikiServices;
use MediaWiki\Output\OutputPage;
use MediaWiki\Page\Article;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Status\Status;
use MediaWiki\TimedMediaHandler\Handlers\TextHandler\TextHandler;
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

	private LanguageNameUtils $languageNameUtils;

	/**
	 * The file associated with this subtitle page
	 */
	private ?File $correspondingFile = null;
	private ?Title $correspondingFileTitle;

	/**
	 * The TimedText format extracted from this page's title
	 */
	private ?string $timedTextFormat;

	/**
	 * The language key extracted from this page's title
	 */
	private ?string $languageKey;

	/**
	 * Status result of the view rendering
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

		$languageKey = $this->getLanguageKey();
		// Look up the language name for the language that these subtitles use:
		if ( $languageKey !== null ) {
			$language = $out->getLanguage()->getCode();
			$languages = $this->languageNameUtils->getLanguageNames( $language, LanguageNameUtils::ALL );
			$languageName = $languages[ $languageKey ] ?? $languageKey;
		} else {
			$languageName = '';
		}

		// Set title of the page
		$message = $this->getPage()->exists() ?
			'timedmedia-timedtext-title-edit-subtitles' :
			'timedmedia-timedtext-title-create-subtitles';
		$out->setPageTitleMsg( wfMessage( $message, $languageName, $this->getCorrespondingFileTitle() ?? '' ) );

		// Attempt to render the content
		$fileHtml = $this->getFileHTML();
		$timedTextHtml = $this->getTimedTextHTML( $out, $languageName );

		// Generate the page
		$warningsAndErrors = $this->getErrorsAndWarnings( $this->renderStatus );
		$out->addHTML( $warningsAndErrors );
		if ( $warningsAndErrors ) {
			$out->addModuleStyles( [
				'mediawiki.codex.messagebox.styles'
			] );
		}
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
	 * @throws MWException
	 */
	private function doRedirectToPageForm( OutputPage $out ): void {
		$context = $out->getContext();
		$lang = $context->getLanguage();
		$file = $this->getCorrespondingFile();

		// Set the page title:
		$out->setPageTitleMsg( wfMessage( 'timedmedia-subtitle-new' ) );

		if ( $file && !$file->isLocal() ) {
			// Add styles for warning messages
			$out->addModuleStyles( [
				'mediawiki.codex.messagebox.styles'
			] );
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

		$languages = $this->languageNameUtils->getLanguageNames();

		// Languages that already have one or more subtitle pages for this file.
		$existingSources = $file && $file->isLocal()
			? ( new TextHandler( $file ) )->getExistingSources()
			: [];

		$out->enableOOUI();

		// First form: jump to an existing subtitle page to view or edit it.
		if ( $existingSources ) {
			$editDescriptor = [
				'existing' => [
					'label-message' => 'timedmedia-subtitle-edit-desc',
					'type' => 'select',
					'options' => $this->getExistingSourceOptions( $existingSources, $languages ),
				],
			];
			$editForm = HTMLForm::factory( 'ooui', $editDescriptor, $context );
			$editForm
				->setMethod( 'post' )
				->setFormIdentifier( 'edit-subtitle' )
				->setSubmitTextMsg( 'timedmedia-subtitle-edit-go' )
				->setWrapperLegendMsg( 'timedmedia-subtitle-edit-legend' )
				->prepareForm()
				->setSubmitCallback( [ $this, 'onSubmitEdit' ] )
				->addPreHtml( $this->getErrorsAndWarnings( $this->renderStatus ) )
				->show();
			if ( $out->getRedirect() !== '' ) {
				return;
			}
		}

		// Second form: create a new translation in a language that does not
		// yet have a subtitle page for this file.
		$newOptions = [];
		foreach ( $languages as $code => $name ) {
			if ( isset( $existingSources[$code] ) ) {
				continue;
			}
			$display = LanguageCode::bcp47( $code ) . ' - ' . $name;
			$newOptions[$display] = $code;
		}

		$langField = [
			'label-message' => 'timedmedia-subtitle-new-desc',
			'required' => true,
			'type' => 'select',
			'options' => $newOptions,
		];
		// Preselect the user's language, unless it already has a subtitle (in
		// which case it isn't among the options and OOUI picks the first one).
		if ( !isset( $existingSources[$lang->getCode()] ) ) {
			$langField['default'] = $lang->getCode();
		}

		$createDescriptor = [
			'lang' => $langField,
			'format' => [
				'label-message' => 'timedmedia-subtitle-new-format',
				'required' => true,
				'type' => 'select',
				'options' => [
					$context->msg( 'timedmedia-subtitle-format-vtt' )->text() => self::VTT_SUBTITLE_FORMAT,
					$context->msg( 'timedmedia-subtitle-format-srt' )->text() => self::SRT_SUBTITLE_FORMAT,
				],
				'default' => self::VTT_SUBTITLE_FORMAT,
			],
		];

		$createForm = HTMLForm::factory( 'ooui', $createDescriptor, $context );
		$createForm
			->setMethod( 'post' )
			->setFormIdentifier( 'create-subtitle' )
			->setSubmitTextMsg( 'timedmedia-subtitle-new-go' )
			->setWrapperLegendMsg( 'timedmedia-subtitle-new-legend' )
			->prepareForm()
			->setSubmitCallback( [ $this, 'onSubmit' ] );
		if ( !$existingSources ) {
			$createForm->addPreHtml( $this->getErrorsAndWarnings( $this->renderStatus ) );
		}
		$createForm->show();
	}

	/**
	 * Build the option list for a select of existing subtitle sources.
	 *
	 * Keys are human readable labels ("en - English (SRT)"), values are the
	 * "<lang>.<format>" suffix that identifies the subtitle page.
	 *
	 * @param array<string,string[]> $existingSources Map of language code to
	 *  the list of formats that exist for it, as returned by
	 *  TextHandler::getExistingSources()
	 * @param array<string,string> $languages Map of language code to display name
	 */
	private function getExistingSourceOptions( array $existingSources, array $languages ): array {
		$options = [];
		foreach ( $existingSources as $code => $formats ) {
			$name = $languages[$code] ?? $code;
			sort( $formats );
			foreach ( $formats as $format ) {
				$display = LanguageCode::bcp47( $code ) . ' - ' . $name .
					' (' . strtoupper( $format ) . ')';
				$options[$display] = $code . '.' . $format;
			}
		}
		return $options;
	}

	/**
	 * Handle the "edit existing subtitles" form: redirect
	 * to the selected subtitle page. The submitted value is the
	 * "<lang>.<format>" suffix relative to the corresponding file.
	 */
	public function onSubmitEdit( array $data ): bool {
		$fileTitle = $this->getCorrespondingFileTitle();
		if ( empty( $data['existing'] ) || !$fileTitle ) {
			return false;
		}
		$target = Title::makeTitleSafe(
			NS_TIMEDTEXT,
			$fileTitle->getDBkey() . '.' . $data['existing']
		);
		if ( !$target ) {
			return false;
		}
		$this->getContext()->getOutput()->redirect( $target->getFullURL() );
		return true;
	}

	/** @inheritDoc */
	public function onSubmit( array $data ): bool {
		if ( !empty( $data['lang'] ) ) {
			$format = $data['format'] ?? self::VTT_SUBTITLE_FORMAT;
			if ( !in_array( $format, static::$knownTimedTextExtensions, true ) ) {
				$format = self::SRT_SUBTITLE_FORMAT;
			}
			$output = $this->getContext()->getOutput();
			$suffix = '.' . $data['lang'] . '.' . $format;
			$target = $output->getTitle() . $suffix;
			$targetFullUrl = $output->getTitle()->getFullUrl() . $suffix;
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
		foreach ( $status->getMessages( 'error' ) as $msg ) {
			$results[] = Html::errorBox( $this->getContext()->msg( $msg )->parse() );
		}
		foreach ( $status->getMessages( 'warning' ) as $msg ) {
			$results[] = Html::warningBox( $this->getContext()->msg( $msg )->parse() );
		}
		return implode( "\n", $results );
	}

	/**
	 * Gets the video HTML ( with the current language set as default )
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
	 */
	private function getTimedTextHTML( OutputPage $out, string $languageName ): string {
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
	 */
	public function getCorrespondingFileTitle(): ?Title {
		return $this->correspondingFileTitle;
	}

	/**
	 * Returns the extension/timedtext type, based on the page title
	 */
	public function getTimedTextFormat(): ?string {
		return $this->timedTextFormat;
	}

	/**
	 * Only pages that end with .languageKey.srt
	 * are known allowed names for TimedText pages.
	 */
	public function isActualTimedTextTitle(): bool {
		return (bool)$this->getTimedTextFormat();
	}

	/**
	 * Returns the language key code from the page title, if present
	 */
	public function getLanguageKey(): ?string {
		return $this->languageKey;
	}

}
