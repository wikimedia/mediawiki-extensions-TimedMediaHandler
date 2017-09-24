<?php
/**
 * Adds iframe output ( bug 25862 )
 *
 * This enables iframe based embeds of the wikimeia player with the following syntax:
 *
 * <iframe src="http://commons.wikimedia.org/wiki/File:Folgers.ogv?embedplayer=yes"
 *     width="240" height="180" frameborder="0" ></iframe>
 *
 */

class TimedMediaIframeOutput {
	/**
	 * The iframe hook check file pages embedplayer=yes
	 * @param Title &$title
	 * @param Article &$article
	 * @param bool $doOutput
	 * @return bool
	 */
	static function iframeHook( &$title, &$article, $doOutput = true ) {
		global $wgRequest, $wgOut, $wgEnableIframeEmbed;
		if ( !$wgEnableIframeEmbed ) {
			// continue normal output iframes are "off" (maybe throw a warning in the future)
			return true;
		}

		// Make sure we are in the right namespace and iframe=true was called:
		if ( is_object( $title ) && $title->getNamespace() == NS_FILE &&
			$wgRequest->getVal( 'embedplayer' ) == 'yes' &&
			$wgEnableIframeEmbed &&
			$doOutput
		) {
			if ( self::outputIframe( $title ) ) {
				// Turn off output of anything other than the iframe
				$wgOut->disable();
			}
		}

		return true;
	}

	/**
	 * Output an iframe
	 * @param Title $title
	 * @return bool
	 * @throws Exception
	 */
	static function outputIframe( $title ) {
		global $wgEnableIframeEmbed, $wgOut, $wgBreakFrames;

		if ( !$wgEnableIframeEmbed ) {
			return false;
		}

		// Setup the render parm
		$file = wfFindFile( $title );
		if ( !$file ) {
			// file was removed, show wiki page with warning
			return false;
		}
		$params = [
			'fillwindow' => true
		];
		$videoTransform = $file->transform( $params );

		// Definitely do not want to break frames
		$wgBreakFrames = false;
		$wgOut->allowClickjacking();
		$wgOut->disallowUserJs();

		if ( TimedMediaHandlerHooks::activePlayerMode() === 'mwembed' ) {
			$wgOut->addModules( [ 'mw.MediaWikiPlayer.loader', 'ext.tmh.embedPlayerIframe' ] );
		}

		if ( TimedMediaHandlerHooks::activePlayerMode() === 'videojs' ) {
			$wgOut->addModules( 'ext.tmh.player' );
			$wgOut->addModuleStyles( 'ext.tmh.player.styles' );
		}
		$wgOut->addModuleStyles( 'embedPlayerIframeStyle' );

		$wgOut->sendCacheControl();
		$rlClient = $wgOut->getRlClient();

		// Stripped-down version of OutputPage::headElement()
		// No skin modules are enqueued because we never call $wgOut->output()
		$pieces = [
			Html::htmlHeader( $rlClient->getDocumentAttributes() ),

			Html::openElement( 'head' ),

			Html::element( 'meta', [ 'charset' => 'UTF-8' ] ),
			Html::element( 'title', null, $title->getText() ),
			$wgOut->getRlClient()->getHeadHtml(),
			implode( "\n", $wgOut->getHeadLinksArray() ),

			Html::closeElement( 'head' ),
		];

		echo implode( "\n", $pieces );
	?>
<body>
		<div id="videoContainer">
			<?php echo $videoTransform->toHtml(); ?>
		</div>
	<?php echo $wgOut->getBottomScripts(); ?>
</body>
</html>
	<?php
		return true;
	}

}
