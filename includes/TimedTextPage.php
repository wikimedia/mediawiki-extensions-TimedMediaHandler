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
use Html;
use HTMLForm;
use LanguageCode;
use MediaWiki\Languages\LanguageNameUtils;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use Message;
use OutputPage;
use TextContent;
use Title;

class TimedTextPage extends Article {

	/** @var int The width of the video plane. Must much the CSS */
	private static $videoWidth = 400;

	/** @var string[] */
	private static $knownTimedTextExtensions = [ 'srt', 'vtt' ];

	/**
	 * @var LanguageNameUtils
	 */
	private $languageNameUtils;

	public function __construct( Title $title, $oldId = null ) {
		parent::__construct( $title, $oldId );
		$services = MediaWikiServices::getInstance();
		$this->languageNameUtils = $services->getLanguageNameUtils();
	}

	public function view(): void {
		$request = $this->getContext()->getRequest();
		$out = $this->getContext()->getOutput();
		$diff = $request->getVal( 'diff' );

		// Article flag is required for some editors, and other features (T307218).
		$out->setArticleFlag( true );

		if ( isset( $diff ) || $this->getTitle()->getNamespace() !== NS_TIMEDTEXT ) {
			parent::view();
			return;
		}
		$this->renderOutput( $out );
	}

	/**
	 * Render TimedText to given output
	 * @param OutputPage $out
	 */
	public function renderOutput( OutputPage $out ): void {
		// parse page title:
		$titleParts = explode( '.', $this->getTitle()->getDBkey() );
		$timedTextExtension = array_pop( $titleParts );
		$languageKey = array_pop( $titleParts );

		$oldid = $this->getOldID();
		# Are we looking at an old revision
		if ( $oldid && $this->fetchRevisionRecord() ) {
			$out->setRevisionId( $this->getRevIdFetched() );
			$this->setOldSubtitle( $oldid );

			if ( !$this->showDeletedRevisionHeader() ) {
				wfDebug( __METHOD__ . ": cannot view deleted revision\n" );
				return;
			}
		}

		$repoGroup = MediaWikiServices::getInstance()->getRepoGroup();
		// Check for File name without text extension:
		// i.e TimedText:myfile.ogg
		$fileTitle = Title::newFromText( $this->getTitle()->getDBkey(), NS_FILE );
		$file = $repoGroup->findFile( $fileTitle );
		// Check for a valid srt page, present redirect form for the full title match:
		if ( $file && $file->exists() &&
			!in_array( $timedTextExtension, self::$knownTimedTextExtensions, true )
		) {
			if ( $file->isLocal() ) {
				$this->doRedirectToPageForm();
			} else {
				$this->doLinkToRemote( $file );
			}
			return;
		}

		// Check for File name with text extension ( from remaining parts of title )
		// i.e TimedText:myfile.ogg.en.srt

		$videoTitle = Title::newFromText( implode( '.', $titleParts ), NS_FILE );

		// Check for remote file
		$basefile = $repoGroup->findFile( $videoTitle );
		if ( !$basefile ) {
			$out->addHTML( wfMessage( 'timedmedia-subtitle-no-video' )->escaped() );
			return;
		}

		if ( !$basefile->isLocal() ) {
			$this->doLinkToRemote( $basefile );
			return;
		}

		// Look up the language name:
		$language = $out->getLanguage()->getCode();
		$languages = $this->languageNameUtils->getLanguageNames( $language, LanguageNameUtils::ALL );
		$languageName = $languages[$languageKey] ?? $languageKey;

		// Set title
		$message = $this->getPage()->exists() ?
			'timedmedia-timedtext-title-edit-subtitles' :
			'timedmedia-timedtext-title-create-subtitles';
		$out->setPageTitle( wfMessage( $message, $languageName, $videoTitle ) );

		$out->addHtml(
			Html::rawElement( 'div', [ 'class' => 'mw-timedtextpage-layout' ],
				Html::rawElement( 'div', [ 'class' => 'mw-timedtextpage-video' ],
					$this->getVideoHTML( $videoTitle )
				) .
				Html::rawElement(
					'div',
					[ 'class' => 'mw-timedtextpage-tt' ],
					$this->getTimedTextHTML( $languageName )
				)
			)
		);
		$out->addModuleStyles( [ 'ext.tmh.timedtextpage.styles' ] );

		if ( !$oldid ) {
			// Set wgRevision at the end from what we actually fetched.
			$out->setRevisionId( $this->getRevIdFetched() );
		}
	}

	/**
	 * Timed text or file is hosted on remote repo, Add a short description and link to foring repo
	 * @param File $file the base file
	 */
	private function doLinkToRemote( File $file ): void {
		$output = $this->getContext()->getOutput();
		$output->setPageTitle( wfMessage( 'timedmedia-subtitle-remote',
			$file->getRepo()->getDisplayName() ) );
		$output->addHTML( wfMessage( 'timedmedia-subtitle-remote-link',
			$file->getDescriptionUrl(), $file->getRepo()->getDisplayName() )->parse() );
	}

	private function doRedirectToPageForm(): void {
		$context = $this->getContext();
		$lang = $context->getLanguage();
		$out = $context->getOutput();

		// Set the page title:
		$out->setPageTitle( wfMessage( 'timedmedia-subtitle-new' ) );

		$out->enableOOUI();

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
			'lang' => [
				'label-message' => 'timedmedia-subtitle-new-desc',
				'required' => true,
				'type' => 'select',
				'options' => $options,
				'default' => $lang->getCode(),
			]
		];

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $context );
		$htmlForm
			->setMethod( 'post' )
			->setSubmitTextMsg( 'timedmedia-subtitle-new-go' )
			->prepareForm()
			->setSubmitCallback( [ $this, 'onSubmit' ] )
			->show();
	}

	public function onSubmit( array $data ): bool {
		if ( !empty( $data['lang'] ) ) {
			$output = $this->getContext()->getOutput();
			$target = $output->getTitle() . '.' . $data['lang'] . '.srt';
			$targetFullUrl = $output->getTitle()->getFullUrl() . '.' . $data['lang'] . '.srt';
			if ( Title::newFromText( $target )->exists() ) {
				$output->redirect( $targetFullUrl );
			} else {
				$output->redirect( $targetFullUrl . '?action=edit' );
			}
			return true;
		}
		return false;
	}

	/**
	 * Gets the video HTML ( with the current language set as default )
	 * @param LinkTarget $videoTitle
	 * @return string
	 */
	private function getVideoHTML( LinkTarget $videoTitle ): string {
		// Get the video embed:
		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $videoTitle );
		if ( !$file ) {
			return wfMessage( 'timedmedia-subtitle-no-video' )->escaped();
		}

		$videoTransform = $file->transform(
			[
				'width' => self::$videoWidth
			]
		);
		return $videoTransform->toHtml();
	}

	/**
	 * Gets an HTML representation of the Timed Text
	 *
	 * @param string $languageName
	 * @return Message|string
	 */
	private function getTimedTextHTML( string $languageName ) {
		if ( !$this->getPage()->exists() ) {
			return wfMessage( 'timedmedia-subtitle-no-subtitles',  $languageName );
		}

		$revision = $this->fetchRevisionRecord();
		if ( !$revision ) {
			return wfMessage( 'noarticletext', $languageName );
		}

		$content = $revision->getContent(
			SlotRecord::MAIN,
			RevisionRecord::FOR_THIS_USER,
			$this->getContext()->getUser()
		);
		if ( !$content ) {
			return wfMessage( 'rev-deleted-text-permission', $languageName );
		}

		return Html::element(
			'pre',
			[],
			( $content instanceof TextContent ) ? $content->getText() : null
		);
	}
}
