<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\Config\Config;
use MediaWiki\FileRepo\File\File;
use MediaWiki\FileRepo\RepoGroup;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\Page\PageIdentity;
use MediaWiki\TimedMediaHandler\Handlers\MP4Handler\MP4Handler;
use MediaWiki\TimedMediaHandler\Handlers\MPEGHandler\MPEGHandler;
use MediaWiki\TimedMediaHandler\Handlers\OggHandler\OggHandler;
use MediaWiki\TimedMediaHandler\Handlers\WebMHandler\WebMHandler;

/**
 * @ingroup Extensions
 */
class TranscodableChecker {
	public function __construct(
		private readonly Config $config,
		private readonly RepoGroup $repoGroup,
	) {
	}

	/**
	 * Wraps the isTranscodableFile function
	 */
	public function isTranscodableTitle( PageIdentity|LinkTarget $title ): bool {
		if ( $title->getNamespace() !== NS_FILE ) {
			return false;
		}
		$file = $this->repoGroup->findFile( $title, [ 'ignoreRedirect' => true ] );
		return $this->isTranscodableFile( $file );
	}

	/**
	 * Utility function to check if a given file can be "transcoded"
	 * @param File|false $file File object
	 */
	public function isTranscodableFile( $file ): bool {
		// don't show the transcode table if transcode is disabled
		if ( !$this->config->get( 'EnableTranscode' ) ) {
			return false;
		}
		// We can only transcode local files
		if ( !$file || !$file->isLocal() ) {
			return false;
		}

		$handler = $file->getHandler();
		// Not able to transcode files without handler
		if ( !$handler ) {
			return false;
		}

		$isAudio = $handler instanceof TimedMediaHandler && $handler->isAudio( $file );
		// If webm, ogg, mp4 or mpeg handler and not audio we can "transcode" this file
		if ( ( $handler instanceof WebMHandler
			|| $handler instanceof OggHandler
			|| $handler instanceof MP4Handler
			|| $handler instanceof MPEGHandler
		) && !$isAudio
		) {
			return true;
		}
		if ( $isAudio && count( $this->config->get( 'EnabledAudioTranscodeSet' ) ) ) {
			return true;
		}
		return false;
	}
}
