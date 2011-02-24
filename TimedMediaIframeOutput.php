<?php 
/** 
 * Adds iframe output ( bug 25862 ) 
 * 
 * This enables iframe based embeds of the wikimeia player with the following syntax:
 *  
 * <iframe src="http://commons.wikimedia.org/wiki/File:Folgers.ogv?embedplayer=yes"
 * 		width="240" height="180" frameborder="0" ></iframe>
 * 
 */

class TimedMediaIframeOutput {	
	/**
	 * The iframe hook check file pages embedplayer=yes
	 */
	static function iframeHook( &$title, &$article, $doOutput = true ) {
		global $wgTitle, $wgRequest, $wgOut, $wgEnableIframeEmbed;
		if( !$wgEnableIframeEmbed )
			return true; //continue normal output iframes are "off" (maybe throw a warning in the future)

		// Make sure we are in the right namespace and iframe=true was called:
		if(	is_object( $wgTitle ) && $wgTitle->getNamespace() == NS_FILE  &&
			$wgRequest->getVal('embedplayer') == 'yes' &&
			$wgEnableIframeEmbed &&
			$doOutput ){
				self::outputIframe( $title );
				// Shut down the database
				wfGetLBFactory()->shutdown();		
				exit();
		}
		
		return true;
	}
	/**
	 * Output an iframe
	 */
	static function outputIframe( $title ) {
		global $wgEnableIframeEmbed, $wgOut, $wgUser,
			$wgEnableScriptLoader;
	
		if(!$wgEnableIframeEmbed){
			throw new MWException( __METHOD__ .' is not enabled' );
		}
		
		$skin = $wgUser->getSkin();
		$out = new OutputPage();
		
		// Setup the render paramaters
		$file = wfFindFile( $title );	
		$params = array(
			// ( will be resized on load )
			'width' => 400
		);
		
		$thumbName = $file->thumbName( $params );
		$thumbnail = $file->transform( $params );
		// XXX Need to "add modules" for the loader "go"... strange. 
		$out->addModules( array( 'embedPlayerIframeStyle') );
		$out->sendCacheControl();
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $title->getText() ?></title>
	<?php 
		echo Html::element( 'meta', array( 'name' => 'ResourceLoaderDynamicStyles', 'content' => '' ) );
	?>
	<?php
		echo $out->getHeadLinks($skin);
		echo $out->getHeadItems();
	?>
	<style>
	body {
		background-image:url('<?php echo $file->getThumbUrl( $thumbName )?>');
		background-repeat:no-repeat;
		background-attachment:fixed;
		background-position:center;
		background-color:#000;
		background-size: 100%;
	} 
	</style>
</head>
<body>
	<div id="bgimage"></div>
	<div id="videoContainer" style="visibility:hidden">
		<?php echo $thumbnail->toHtml(); ?>
	</div
	<?php echo $out->getHeadScripts($skin); ?>	
	<script type="text/javascript">		
		mw.ready(function(){			
			var fitPlayer = function(){
				$j( '#<?php echo TimedMediaTransformOutput::PLAYER_ID_PREFIX . '0' ?>' )
				.get(0).resizePlayer({
					'width' : $j(window).width(),
					'height' : $j(window).height()
				});
			}
			// Bind window resize to reize the player:
			$j( window ).resize( fitPlayer );	  
			$('#videoContainer').css({
				'visibility':'visible'
			});
			fitPlayer(); 
		});
	</script>
</body>
</html>
	<?php
	}
	
}