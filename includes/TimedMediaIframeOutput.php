<?php
/**
 * Adds iframe output ( bug 25862 )
 *
 * This enables iframe based embeds of the wikimedia player with the following syntax:
 *
 * <iframe src="http://commons.wikimedia.org/wiki/File:Folgers.ogv?embedplayer=yes"
 *     width="240" height="180" frameborder="0" ></iframe>
 *
 */

namespace MediaWiki\TimedMediaHandler;

use Exception;
use Html;
use MediaWiki;
use MediaWiki\Hook\MediaWikiPerformActionHook;
use MediaWiki\MediaWikiServices;
use OutputPage;
use Page;
use Title;
use User;
use WebRequest;

class TimedMediaIframeOutput implements MediaWikiPerformActionHook {
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
	public function onMediaWikiPerformAction( $output, $page, $title, $user, $request, $mediawiki ) {
		global $wgEnableIframeEmbed;
		if ( !$wgEnableIframeEmbed ) {
			// continue normal output iframes are "off" (maybe throw a warning in the future)
			return true;
		}

		// Make sure we are in the right namespace and iframe=true was called:
		if ( is_object( $title ) && $title->getNamespace() === NS_FILE &&
			$request->getVal( 'embedplayer' ) &&
			$this->outputIframe( $title, $output )
		) {
			// Turn off output of anything other than the iframe
			$output->disable();
			return false;
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
	private function outputIframe( $title, $out ) {
		global $wgEnableIframeEmbed, $wgBreakFrames;

		if ( !$wgEnableIframeEmbed ) {
			return false;
		}

		// Setup the render param
		$file = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $title );
		if ( !$file ) {
			// file was removed, show wiki page with warning
			return false;
		}
		$params = [
			'inline' => true,
			'fillwindow' => true,
			'width' => $file->getWidth()
		];

		$videoTransform = $file->transform( $params );

		// Definitely do not want to break frames
		$wgBreakFrames = false;
		$out->setPreventClickjacking( false );
		$out->disallowUserJs();

		$out->addModules( [ 'ext.tmh.player', 'ext.tmh.player.inline' ] );
		$out->addModuleStyles( [ 'ext.tmh.player.inline.styles', 'embedPlayerIframeStyle' ] );

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
