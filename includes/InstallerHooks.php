<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\Installer\DatabaseUpdater;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

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
		$updater->modifyExtensionField(
			'transcode', 'transcode_time_error', $dir . $dbType . '/patch-transcode-transcode_timestamp.sql'
		);

		return true;
	}
}
