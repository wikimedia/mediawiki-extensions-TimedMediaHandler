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
	public function view() {
		global $wgOut, $wgShowEXIF, $wgRequest, $wgUser;
		
		$diff = $wgRequest->getVal( 'diff' );
		$diffOnly = $wgRequest->getBool( 'diffonly', $wgUser->getOption( 'diffonly' ) );

		if ( $this->mTitle->getNamespace() != NS_TIMEDTEXT || ( isset( $diff ) && $diffOnly ) ) {
			return parent::view();
		}
		
		// Set title 
		$wgOut->setPageTitle( wfMsg('mwe-timedtext-language-subtitles-for-clip', ) );

		// Set two page devider 
		
	}
}