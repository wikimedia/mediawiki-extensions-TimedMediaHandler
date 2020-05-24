<?php
/**
 * TimedText page display the current video with subtitles to the right.
 *
 * Future features for this page"
 * @todo add srt download links
 * @todo parse and validate srt files
 * @todo link-in or include the universal subtitles editor
 */

use MediaWiki\MediaWikiServices;

class TimedTextPage extends Article {
	// The width of the video plane:
	private static $videoWidth = 400;
	private static $knownTimedTextExtensions = [ 'srt', 'vtt' ];

	public function view() {
		$request = $this->getContext()->getRequest();
		$out = $this->getContext()->getOutput();
		$user = $this->getContext()->getUser();

		$diff = $request->getVal( 'diff' );

		if ( $this->getTitle()->getNamespace() != NS_TIMEDTEXT || isset( $diff ) ) {
			parent::view();
			return;
		}
		$this->renderOutput( $out );
	}

	/**
	 * Render TimedText to given output
	 * @param OutputPage $out
	 */
	public function renderOutput( $out ) {
		// parse page title:
		$titleParts = explode( '.', $this->getTitle()->getDBkey() );
		$timedTextExtension = array_pop( $titleParts );
		$languageKey = array_pop( $titleParts );

		$oldid = $this->getOldID();
		# Are we looking at an old revision
		if ( $oldid && $this->fetchRevisionRecord() ) {
			$this->fetchContentObject();
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
		if ( !in_array( $timedTextExtension, self::$knownTimedTextExtensions ) &&
			$file && $file->exists()
		) {
			if ( $file->isLocal() ) {
				$this->doRedirectToPageForm();
			} else {
				$this->doLinkToRemote( $file );
			}
			return;
		}

		// Check for File name with text extension ( from remaning parts of title )
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
		$languages = Language::fetchLanguageNames( $language, 'all' );
		if ( isset( $languages[ $languageKey ] ) ) {
			$languageName = $languages[ $languageKey ];
		} else {
			$languageName = $languageKey;
		}

		// Set title
		$message = $this->getPage()->exists() ?
			'mwe-timedtext-language-subtitles-for-clip' :
			'mwe-timedtext-language-no-subtitles-for-clip';
		$out->setPageTitle( wfMessage( $message, $languageName, $videoTitle ) );

		// Get the video with with a max of 600 pixel page
		$out->addHTML(
			Xml::tags( 'table', [ 'style' => 'border:none' ],
				Xml::tags( 'tr', null,
					Xml::tags( 'td', [ 'valign' => 'top',  'width' => self::$videoWidth ],
						$this->getVideoHTML( $videoTitle )
					) .
					Xml::tags( 'td', [ 'valign' => 'top' ], $this->getTimedTextHTML( $languageName ) )
				)
			)
		);

		if ( !$oldid ) {
			// Set wgRevision at the end from what we actually fetched.
			$out->setRevisionId( $this->getRevIdFetched() );
		}
	}

	/**
	 * Timed text or file is hosted on remote repo, Add a short description and link to foring repo
	 * @param File $file the base file
	 */
	private function doLinkToRemote( $file ) {
		$output = $this->getContext()->getOutput();
		$output->setPageTitle( wfMessage( 'timedmedia-subtitle-remote',
			$file->getRepo()->getDisplayName() ) );
		$output->addHTML( wfMessage( 'timedmedia-subtitle-remote-link',
			$file->getDescriptionUrl(), $file->getRepo()->getDisplayName() )->parse() );
	}

	private function doRedirectToPageForm() {
		$context = $this->getContext();
		$lang = $context->getLanguage();
		$out = $context->getOutput();

		// Set the page title:
		$out->setPageTitle( wfMessage( 'timedmedia-subtitle-new' ) );

		$out->enableOOUI();

		$languages = Language::fetchLanguageNames( null, 'mwfile' );
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

	public function onSubmit( array $data ) {
		if ( !empty( $data['lang'] ) ) {
			$output = $this->getContext()->getOutput();
			$output->redirect(
				$output->getTitle()->getFullUrl() . '.' . $data['lang'] . '.srt?action=edit'
			);
			return true;
		}
		return false;
	}

	/**
	 * Gets the video HTML ( with the current language set as default )
	 * @param string $videoTitle
	 * @return string
	 */
	private function getVideoHTML( $videoTitle ) {
		// Get the video embed:
		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $videoTitle );
		if ( !$file ) {
			return wfMessage( 'timedmedia-subtitle-no-video' )->escaped();
		} else {
			$videoTransform = $file->transform(
				[
					'width' => self::$videoWidth
				]
			);
			return $videoTransform->toHTML();
		}
	}

	/**
	 * Gets an HTML representation of the Timed Text
	 *
	 * @param string $languageName
	 * @return Message|string
	 */
	private function getTimedTextHTML( $languageName ) {
		if ( !$this->getPage()->exists() ) {
			return wfMessage( 'timedmedia-subtitle-no-subtitles',  $languageName );
		}
		return Xml::element(
			'pre',
			[ 'style' => 'margin-top: 0px;' ],
			ContentHandler::getContentText( $this->getContentObject() ),
			false
		);
	}
}
