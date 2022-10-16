<?php

namespace MediaWiki\TimedMediaHandler;

use DatabaseUpdater;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

/**
 * Hooks for installer
 *
 * @file
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
		if ( $dbType === 'mysql' ) {
			$updater->addExtensionTable( 'transcode',
				$dir . 'tables-generated.sql'
			);
		} elseif ( $dbType === 'sqlite' ) {
			$updater->addExtensionTable( 'transcode',
				$dir . 'sqlite/tables-generated.sql'
			);
		} elseif ( $dbType === 'postgres' ) {
			$updater->addExtensionTable( 'transcode',
				$dir . 'postgres/tables-generated.sql'
			);
		}
		$dirPatch = $dbType === 'mysql' ? $dir : $dir . $dbType . '/';

		// 1.38
		$updater->modifyExtensionField(
			'transcode', 'transcode_time_error', $dirPatch . 'patch-transcode-transcode_timestamp.sql'
		);

		return true;
	}
}
