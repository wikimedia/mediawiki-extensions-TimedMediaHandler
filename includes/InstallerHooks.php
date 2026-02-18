<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\Installer\DatabaseUpdater;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;
use MigrateTranscodeStates;

/**
 * Hooks for installer
 *
 * @ingroup Extensions
 */
class InstallerHooks implements LoadExtensionSchemaUpdatesHook {
	/**
	 * @param DatabaseUpdater $updater
	 * @return bool
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$dir = dirname( __DIR__ ) . '/sql/';
		$dbType = $updater->getDB()->getType();

		$updater->addExtensionTable( 'transcode', $dir . $dbType . '/tables-generated.sql' );

		// 1.38
		$updater->modifyExtensionField(
			'transcode', 'transcode_time_error', $dir . $dbType . '/patch-transcode-transcode_timestamp.sql'
		);

		// 1.46
		$updater->addExtensionField(
			'transcode', 'transcode_state', $dir . $dbType . '/patch-transcode-state-size.sql'
		);
		$updater->addExtensionUpdate( [
			'runMaintenance',
			MigrateTranscodeStates::class,
		] );

		return true;
	}
}
