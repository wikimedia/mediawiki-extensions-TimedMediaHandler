<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\MainConfigNames;
use MediaWiki\Settings\SettingsBuilder;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePresets;

class RegistrationCallback {
	/**
	 * Modify config via registration callback
	 */
	public static function register( array $extInfo, SettingsBuilder $settingsBuilder ): void {
		$tmhFileExtensions = $settingsBuilder->getConfig()->get( 'TmhFileExtensions' );

		// Remove mp4 if not enabled:
		if ( !$settingsBuilder->getConfig()->get( 'TmhEnableMp4Uploads' ) ) {
			$index = array_search( 'mp4', $tmhFileExtensions, true );
			if ( $index !== false ) {
				array_splice( $tmhFileExtensions, $index, 1 );
			}
		}
		$settingsBuilder->putConfigValue( MainConfigNames::FileExtensions, $tmhFileExtensions );

		// Transcode jobs must be explicitly requested from the job queue:
		$settingsBuilder->putConfigValue( MainConfigNames::JobTypesExcludedFromDefaultQueue, [ 'webVideoTranscode' ] );

		// validate enabled transcodeset values
		TranscodePresets::validateTranscodeConfiguration( $settingsBuilder->getConfig() );
	}
}
