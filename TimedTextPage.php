<?php
/**
 * TimedText page display the current video with subtitles to the right.
 *
 * Future features for this page"
 *  @todo add srt download links
 *  @todo parse and validate srt files
 *  @todo link-in or include the universal subtitles editor
 */
class TimedTextPage extends Article {
	// The width of the video plane:
	static private $videoWidth = 400;

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
	 * @param $out OutputPage
	 */
	public function renderOutput( $out ){
		// parse page title:
		$titleParts = explode( '.', $this->getTitle()->getDBkey() );
		$srt = array_pop( $titleParts );
		$languageKey = array_pop( $titleParts );

		$oldid = $this->getOldID();
		# Are we looking at an old revision
		if ( $oldid && $this->mRevision ) {
			$this->fetchContentObject();
			$out->setRevisionId( $this->getRevIdFetched() );
			$this->setOldSubtitle( $oldid );

			if ( !$this->showDeletedRevisionHeader() ) {
				wfDebug( __METHOD__ . ": cannot view deleted revision\n" );
				return;
			}
		}

		// Check for File name without text extension:
		// i.e TimedText:myfile.ogg
		$fileTitle = Title::newFromText( $this->getTitle()->getDBkey(), NS_FILE );
		$file = wfFindFile( $fileTitle );
		// Check for a valid srt page, present redirect form for the full title match:
		if( $srt !== '.srt' && $file && $file->exists() ){
			if( $file->isLocal() ){
				$this->doRedirectToPageForm( $fileTitle );
			} else {
				$this->doLinkToRemote( $file );
			}
			return;
		}

		// Check for File name with text extension ( from remaning parts of title )
		// i.e TimedText:myfile.ogg.en.srt

		$videoTitle = Title::newFromText( implode('.', $titleParts ), NS_FILE );

		// Check for remote file
		$basefile = wfFindFile( $videoTitle );
		if ( !$basefile ) {
			$out->addHTML( wfMessage( 'timedmedia-subtitle-no-video' )->escaped() );
			return;
		}

		if( !$basefile->isLocal() ){
			$this->doLinkToRemote( $basefile );
			return;
		}

		// Look up the language name:
		$language = $out->getLanguage()->getCode();
		$languages = Language::fetchLanguageNames( $language, 'all' );
		if( isset( $languages[ $languageKey ] ) ) {
			$languageName = $languages[ $languageKey ];
		} else {
			$languageName = $languageKey;
		}

		// Set title
		$message = $this->exists() ?
			'mwe-timedtext-language-subtitles-for-clip' :
			'mwe-timedtext-language-no-subtitles-for-clip';
		$out->setPageTitle( wfMessage($message, $languageName, $videoTitle ) );

		// Get the video with with a max of 600 pixel page
		$out->addHTML(
			xml::tags( 'table', array( 'style'=> 'border:none' ),
				xml::tags( 'tr', null,
					xml::tags( 'td', array( 'valign' => 'top',  'width' => self::$videoWidth ),
						$this->getVideoHTML( $videoTitle )
					) .
					xml::tags( 'td', array( 'valign' => 'top' ) , $this->getSrtHTML( $languageName ) )
				)
			)
		);
	}

	/**
	 * Timed text or file is hosted on remote repo, Add a short description and link to foring repo
	 * @param $file File the base file
	 */
	function doLinkToRemote( $file ){
		$output = $this->getContext()->getOutput();
		$output->setPageTitle( wfMessage( 'timedmedia-subtitle-remote',
			$file->getRepo()->getDisplayName() ) );
		$output->addHTML( wfMessage( 'timedmedia-subtitle-remote-link',
			$file->getDescriptionUrl(), $file->getRepo()->getDisplayName()  ) );
	}

	function doRedirectToPageForm(){
		$lang = $this->getContext()->getLanguage();
		$out = $this->getContext()->getOutput();

		// Set the page title:
		$out->setPageTitle( wfMessage( 'timedmedia-subtitle-new' ) );

		// Look up the language name:
		$language = $out->getLanguage()->getCode();
		$attrs = array( 'id' => 'timedmedia-tt-input' );
		$langSelect = Xml::languageSelector( $language, false, null, $attrs, null );

		$out->addHTML(
			Xml::tags('div', array( 'style' => 'text-align:center' ),
				Xml::tags( 'div', null,
					wfMessage( 'timedmedia-subtitle-new-desc', $lang->getCode() )->parse()
				) .
				$langSelect[1] .
				Xml::tags( 'button',
					array( 'id' => 'timedmedia-tt-go' ),
					wfMessage( 'timedmedia-subtitle-new-go' )->escaped()
				)
			)
		);
		$timedTextTile = Title::newFromText( $this->getTitle()->getDBkey() . '.'.
			'LANG' . '.srt', NS_TIMEDTEXT )->getFullText();
		$out->addScript(
			Html::InlineScript(
				'$(function() {' .
					'$("#timedmedia-tt-go").click(function(){' .
						'var articlePath = mw.config.get( "wgArticlePath" );' .
						'var paramSep = (articlePath.indexOf("?")===-1) ? "?" : "&";' .
						'var title = ' . json_encode( $timedTextTile ) . '.replace("LANG", $("#timedmedia-tt-input").val());'.
						'window.location = articlePath.replace(/\$1/, mw.util.wikiUrlencode( title ) + ' .
						' paramSep + "action=edit" )  ' .
					'});' .
				'});'
			)
		);
	}

	/**
	 * Gets the video HTML ( with the current language set as default )
	 * @param $videoTitle string
	 * @return String
	 */
	private function getVideoHTML( $videoTitle ){
		// Get the video embed:
		$file = wfFindFile( $videoTitle );
		if( !$file ){
			return wfMessage( 'timedmedia-subtitle-no-video' )->escaped();
		} else {
			$videoTransform= $file->transform(
				array(
					'width' => self::$videoWidth
				)
			);
			return $videoTransform->toHTML();
		}
	}

	/**
	 * Gets the srt text
	 *
	 * XXX We should add srt parsing and links to seek to that time in the video
	 * @param $languageName string
	 * @return Message|string
	 */
	private function getSrtHTML( $languageName ){
		if( !$this->exists() ){
			return wfMessage( 'timedmedia-subtitle-no-subtitles',  $languageName );
		}
		return Xml::element(
			'pre',
			array( 'style' => 'margin-top: 0px;' ),
			$this->getContent(),
			false
		);
	}
}
