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
		global $wgOut, $wgRequest, $wgUser;

		$diff = $wgRequest->getVal( 'diff' );
		$diffOnly = $wgRequest->getBool( 'diffonly', $wgUser->getOption( 'diffonly' ) );

		if ( $this->getTitle()->getNamespace() != NS_TIMEDTEXT || ( isset( $diff ) && $diffOnly ) ) {
			parent::view();
			return;
		}
		// parse page title:
		$titleParts = explode( '.', $this->getTitle()->getDBKey() );
		$srt = array_pop( $titleParts );
		$languageKey = array_pop( $titleParts );

		// Check for File name without text extension:
		// i.e TimedText:myfile.ogg
		
		$fileTitle = Title::newFromText( $this->getTitle()->getDBKey(), NS_FILE );
		$file = wfFindFile( $fileTitle );
		// Check for a valid srt page, present redirect form for the full title match:
		if( $srt !== '.srt' && $file && $file->exists() ){
			if( $file->isLocal() ){
				$this->doRedirectToPageForm( $fileTitle );
			} else {
				$this->doLinkToRemote( $file );
			}
			return ;
		}

		// Check for File name with text extension ( from remaning parts of title )
		// i.e TimedText:myfile.ogg.en.srt

		$videoTitle = Title::newFromText( implode('.', $titleParts ), NS_FILE );

		// Check for remote file
		$basefile = wfFindFile( $videoTitle );
		if( !$basefile->isLocal() ){
			$this->doLinkToRemote( $basefile );
			return ;
		}

		// Look up the language name:
		$languages = Language::fetchLanguageNames( 'en', 'all' );
		if( isset( $languages[ $languageKey ] ) ) {
			$languageName = $languages[ $languageKey ];
		} else {
			$languageName = $languageKey;
		}

		// Set title
		$wgOut->setPageTitle(
			wfMessage( 'mwe-timedtext-language-subtitles-for-clip', $languageName, $videoTitle )
		);

		// Get the video with with a max of 600 pixel page
		$wgOut->addHTML(
			xml::tags( 'table', array( 'style'=> 'border:none' ),
				xml::tags( 'tr', null,
					xml::tags( 'td', array( 'valign' => 'top',  'width' => self::$videoWidth ), $this->getVideoHTML( $videoTitle ) ) .
					xml::tags( 'td', array( 'valign' => 'top' ) , $this->getSrtHTML( $languageName ) )
				)
			)
		);
	}
	/**
	 * Timed text or file is hosted on remote repo, Add a short description and link to foring repo
	 * @param $file the base file
	 */
	function doLinkToRemote( $file ){
		global $wgOut;
		$remoteName = $file->getRepoName();
		$wgOut->setPageTitle( wfMessage( 'timedmedia-subtitle-remote',  $file->getRepo()->getDisplayName() ) );
		
		$wgOut->addHTML( wfMessage( 'timedmedia-subtitle-remote-link', $file->getDescriptionUrl(), $file->getRepo()->getDisplayName()  ) );
	}
	function doRedirectToPageForm( $fileTitle ){
		global $wgContLang, $wgOut;
		// Set the page title:
		$wgOut->setPageTitle( wfMessage( 'timedmedia-subtitle-new' ) );

		$timedTextTile = Title::newFromText( $this->getTitle()->getDBKey() . '.'.
							$wgContLang->getCode() . '.srt', NS_TIMEDTEXT );
		$wgOut->addHTML(
			Xml::tags('div', array( 'style' => 'text-align:center' ),
				Xml::tags( 'span', null, wfMessage( 'timedmedia-subtitle-new-desc' )->escaped() ) .
				Xml::tags( 'input', array(
					'id' => 'timedmedia-tt-input',
					'value' => $timedTextTile->getFullText(),
					'size' => 50 ),
					Xml::tags( 'button',
						array( 'id' => 'timedmedia-tt-go' ),
						wfMessage( 'timedmedia-subtitle-new-go' )->escaped()
					)
				)
			)
		);
		$wgOut->addScript(
			Html::InlineScript(
				'$(function() {' .
					'$("#timedmedia-tt-go").click(function(){' .
						'var articlePath = mw.config.get( "wgArticlePath" );' .
						'var paramSep = (articlePath.indexOf("?")===-1) ? "?" : "&";' .
						'window.location = articlePath.replace(/\$1/, $( "#timedmedia-tt-input" ).val() + ' .
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
		return '<pre style="margin-top: 0px;">'. $this->getContent() . '</pre>';
	}
}
