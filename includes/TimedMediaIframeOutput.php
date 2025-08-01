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
use MediaWiki\Actions\ActionEntryPoint;
use MediaWiki\Config\Config;
use MediaWiki\FileRepo\RepoGroup;
use MediaWiki\Hook\MediaWikiPerformActionHook;
use MediaWiki\Html\Html;
use MediaWiki\Output\OutputPage;
use MediaWiki\Page\Article;
use MediaWiki\Request\WebRequest;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

class TimedMediaIframeOutput implements MediaWikiPerformActionHook {

	public function __construct(
		private readonly Config $config,
		private readonly RepoGroup $repoGroup,
	) {
	}

	/**
	 * The iframe hook check file pages embedplayer=yes
	 * @param OutputPage $output
	 * @param Article $article
	 * @param Title $title
	 * @param User $user
	 * @param WebRequest $request
	 * @param ActionEntryPoint $mediaWiki
	 * @return bool
	 * @throws Exception
	 */
	public function onMediaWikiPerformAction( $output, $article, $title, $user, $request, $mediaWiki ) {
		if ( !$this->config->get( 'EnableIframeEmbed' ) ) {
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
	 * @throws Exception
	 */
	private function outputIframe( Title $title, OutputPage $out ): bool {
		global $wgBreakFrames;

		// Setup the render param
		$file = $this->repoGroup->findFile( $title );
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
		$out->getMetadata()->setPreventClickjacking( false );
		$out->disallowUserJs();

		$out->addModules( [ 'ext.tmh.player', 'ext.tmh.player.inline' ] );
		$out->addModuleStyles( [ 'embedPlayerIframeStyle' ] );

		$out->sendCacheControl();
		$rlClient = $out->getRlClient();

		// Stripped-down version of OutputPage::headElement()
		// No skin modules are enqueued because we never call $wgOut->output()
		$pieces = [
			Html::htmlHeader( $rlClient->getDocumentAttributes() ),

			Html::openElement( 'head' ),

			Html::element( 'meta', [ 'charset' => 'UTF-8' ] ),
			Html::element( 'title', [], $title->getText() ),
			$rlClient->getHeadHtml(),
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
