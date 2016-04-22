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
	 * @param $title Title
	 * @param $article Article
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
	 * @param $title Title
	 * @throws Exception
	 */
	static function outputIframe( $title ) {
		global $wgEnableIframeEmbed, $wgOut, $wgBreakFrames, $wgTmhWebPlayer;

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

		if ( $wgTmhWebPlayer == 'mwembed' ) {
			$wgOut->addModules( [ 'mw.MediaWikiPlayer.loader', 'ext.tmh.embedPlayerIframe' ] );
		} elseif ( $wgTmhWebPlayer === 'videojs' ) {
			$wgOut->addModuleStyles( 'ext.tmh.player.styles' );
			$wgOut->addModules( 'ext.tmh.player' );
		}
		$wgOut->addModuleStyles( 'embedPlayerIframeStyle' );
		$wgOut->sendCacheControl();
	?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title><?php echo $title->getText() ?></title>
	<?php
		echo implode( "\n", $wgOut->getHeadLinksArray() );
	?>
	<?php
		// In place of buildCssLinks, because we don't want to pull in all the skin CSS etc.
		$links = $wgOut->makeResourceLoaderLink(
			'embedPlayerIframeStyle', ResourceLoaderModule::TYPE_STYLES
		);
		echo implode( "\n", $links["html"] );

		echo Html::element( 'meta', [ 'name' => 'ResourceLoaderDynamicStyles', 'content' => '' ] );
	?>
	<?php echo "\n" . $wgOut->getHeadScripts(); ?>
	</head>
<body>
		<img src="<?php echo $videoTransform->getUrl() ?>" id="bgimage" ></img>
		<div id="videoContainer" style="visibility:hidden">
			<?php echo $videoTransform->toHtml(); ?>
		</div>
	<?php echo $wgOut->getBottomScripts(); ?>
</body>
</html>
	<?php
		return true;
	}

}
