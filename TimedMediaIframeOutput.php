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
		
		// Build the html output:
		$file = wfFindFile( $title );
		$thumb = $file->transform( $videoParam );
		$out = new OutputPage();
		$file->getHandler()->setHeaders( $out );
	
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $title->getText() ?></title>
<style type="text/css">
	body {
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
	}
</style>
	<?php
		// Similar to $out->headElement (but without css)
		echo $out->getHeadScripts();
		echo $out->getHeadLinks();
		echo $out->getHeadItems();
	?>
</head>
<body>
	<?php echo $thumb->toHtml(); ?>
</body>
</html>
	<?php
	}
	
}