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
		if( !$wgEnableIframeEmbed )
			return true; //continue normal output iframes are "off" (maybe throw a warning in the future)

		// Make sure we are in the right namespace and iframe=true was called:
		if(	is_object( $title ) && $title->getNamespace() == NS_FILE  &&
			$wgRequest->getVal('embedplayer') == 'yes' &&
			$wgEnableIframeEmbed &&
			$doOutput ){

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
	 * @throws MWException
	 */
	static function outputIframe( $title ) {
		global $wgEnableIframeEmbed, $wgOut, $wgUser;

		if( !$wgEnableIframeEmbed ){
			return false;
		}

		// Setup the render parm
		$file = wfFindFile( $title );
		if ( !$file ) {
			// file was removed, show wiki page with warning
			return false;
		}
		$params = array(
			'fillwindow' => true
		);
		$videoTransform = $file->transform( $params );

		$wgOut->addModules( array( 'embedPlayerIframeStyle') );
		$wgOut->sendCacheControl();
	?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8" />
<title><?php echo $title->getText() ?></title>
	<?php
		echo Html::element( 'meta', array( 'name' => 'ResourceLoaderDynamicStyles', 'content' => '' ) );
	?>
	<?php
		echo $wgOut->getHeadLinks();
		echo $wgOut->getHeadItems();
	?>
	<style type="text/css">
		html, body {
		  height: 100%;
		  width: 100%;
		  margin: 0;
		  padding: 0;
		  overflow:hidden;
		}
		img#bgimage {
		  position:fixed;
		  top:0;
		  left:0;
		  width:100%;
		  height:100%;
		}
		.videoHolder {
		  position:relative;
		}
	</style>
	<?php echo $wgOut->getHeadScripts(); ?>
	<?php
	echo Html::inlineScript(
	ResourceLoader::makeLoaderConditionalScript(
			Xml::encodeJsCall( 'mw.loader.go', array() )
		)
	);
	?>
	</head>
<body>
	<img src="<?php echo $videoTransform->getUrl() ?>" id="bgimage" ></img>
	<div id="videoContainer" style="visibility:hidden">
		<?php echo $videoTransform->toHtml(); ?>
	</div>
	<script>
		// Turn off rewrite selector
		mw.setConfig('EmbedPlayer.RewriteSelector', '');
		// only enable fullscreen if enabled in iframe
		mw.setConfig('EmbedPlayer.EnableFullscreen', document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || false );
		$('#bgimage').remove();

		mw.setConfig( 'EmbedPlayer.IsIframeServer', true );

		// rewrite player
		$( '#<?php echo TimedMediaTransformOutput::PLAYER_ID_PREFIX . '0' ?>' ).embedPlayer(function(){

			// Bind window resize to reize the player:
			var fitPlayer = function(){
				$( '#<?php echo TimedMediaTransformOutput::PLAYER_ID_PREFIX . '0' ?>' )
				[0].updateLayout();
			}

			$( window ).resize( fitPlayer );
			$('#videoContainer').css({
				'visibility':'visible'
			});
			fitPlayer();
		});
	</script>
</body>
</html>
	<?php
		return true;
	}

}
