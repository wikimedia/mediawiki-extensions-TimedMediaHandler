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

use MediaWiki\MediaWikiServices;

class TimedMediaIframeOutput {
	/**
	 * The iframe hook check file pages embedplayer=yes
	 * @param OutputPage $output
	 * @param Page $page
	 * @param Title $title
	 * @param User $user
	 * @param WebRequest $request
	 * @param MediaWiki $mediawiki
	 * @return bool
	 * @throws Exception
	 */
	public static function iframeHook( $output, $page, $title, $user, $request, $mediawiki ) {
		global $wgEnableIframeEmbed;
		if ( !$wgEnableIframeEmbed ) {
			// continue normal output iframes are "off" (maybe throw a warning in the future)
			return true;
		}

		// Make sure we are in the right namespace and iframe=true was called:
		if ( is_object( $title ) && $title->getNamespace() == NS_FILE &&
			$request->getVal( 'embedplayer' ) == 'yes'
		) {
			if ( self::outputIframe( $title, $output ) ) {
				// Turn off output of anything other than the iframe
				$output->disable();
				return false;
			}
		}

		return true;
	}

	/**
	 * Output an iframe
	 * @param Title $title
	 * @param OutputPage $out
	 * @return bool
	 * @throws Exception
	 */
	private static function outputIframe( $title, $out ) {
		global $wgEnableIframeEmbed, $wgBreakFrames;

		if ( !$wgEnableIframeEmbed ) {
			return false;
		}

		// Setup the render parm
		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $title );
		if ( !$file ) {
			// file was removed, show wiki page with warning
			return false;
		}
		$params = [
			'fillwindow' => true,
			'width' => $file->getWidth()
		];
		if ( TimedMediaHandlerHooks::activePlayerMode() === 'videojs' ) {
			$params['inline'] = true;
		}
		$videoTransform = $file->transform( $params );

		// Definitely do not want to break frames
		$wgBreakFrames = false;
		$out->allowClickjacking();
		$out->disallowUserJs();

		if ( TimedMediaHandlerHooks::activePlayerMode() === 'mwembed' ) {
			$out->addModules( [ 'mw.MediaWikiPlayer.loader', 'ext.tmh.embedPlayerIframe' ] );
		}

		if ( TimedMediaHandlerHooks::activePlayerMode() === 'videojs' ) {
			$out->addModules( 'ext.tmh.player' );
			$out->addModuleStyles( 'ext.tmh.player.styles' );
		}
		$out->addModuleStyles( 'embedPlayerIframeStyle' );

		$out->sendCacheControl();
		$rlClient = $out->getRlClient();

		// Stripped-down version of OutputPage::headElement()
		// No skin modules are enqueued because we never call $wgOut->output()
		$pieces = [
			Html::htmlHeader( $rlClient->getDocumentAttributes() ),

			Html::openElement( 'head' ),

			Html::element( 'meta', [ 'charset' => 'UTF-8' ] ),
			Html::element( 'title', [], $title->getText() ),
			$out->getRlClient()->getHeadHtml(),
			implode( "\n", $out->getHeadLinksArray() ),

			Html::closeElement( 'head' ),
		];

		// @phan-suppress-next-line SecurityCheck-XSS False positive
		echo implode( "\n", $pieces );
	?>
<body>
		<div id="videoContainer">
			<?php echo $videoTransform->toHtml(); ?>
		</div>
	<?php echo $out->getBottomScripts(); ?>
</body>
</html>
	<?php
		return true;
	}

}
