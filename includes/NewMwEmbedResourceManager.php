<?php

/**
 * NewMwEmbedResourceManager adds some convenience functions for loading mwEmbed 'modules'.
 *  Its shared between the mwEmbedStandAlone and the MwEmbed extension
 *
 * @file
 * @ingroup Extensions
 */
class NewMwEmbedResourceManager {
	protected static $moduleSet = [];
	protected static $moduleConfig = [];

	/**
	 * Register mwEmbeed resource set based on the
	 *
	 * Adds modules to ResourceLoader
	 * @param string $resourceListFilePath
	 * @throws Exception
	 */
	public static function register( $resourceListFilePath ) {
		$localResourcePath = dirname( $resourceListFilePath );
		$moduleName = basename( $localResourcePath );

		// Get the mwEmbed module resource registration:
		$resourceList = require $resourceListFilePath;

		// Add the resource list into the module set with its provided path
		self::$moduleSet[$localResourcePath] = $resourceList;
	}

	public static function addConfigDefaults( array $vars ) {
		self::$moduleConfig = array_merge( self::$moduleConfig, $vars );
	}

	/**
	 * @param array &$vars
	 * @return array
	 */
	public static function registerConfigVars( &$vars ) {
		// Allow localSettings.php to override any module config by updating $wgMwEmbedModuleConfig var
		global $wgMwEmbedModuleConfig;
		foreach ( self::$moduleConfig as $key => $value ) {
			if ( !isset( $wgMwEmbedModuleConfig[$key] ) ) {
				$wgMwEmbedModuleConfig[$key] = $value;
			}
		}
		$vars = array_merge( $vars, $wgMwEmbedModuleConfig );

		return $vars;
	}

	/**
	 * ResourceLoaderRegisterModules hook
	 *
	 * TODO: At some point these should be registered in extension.json
	 * But for now we register them dynamically, because they are config dependent,
	 * while we have two players.
	 *
	 * Adds any mwEmbedResources to the ResourceLoader
	 * @param ResourceLoader &$resourceLoader
	 * @return bool
	 */
	public static function registerModules( &$resourceLoader ) {
		foreach ( self::$moduleSet as $localPath => $modules ) {
			foreach ( $modules as $name => $resources ) {
				$resources['localBasePath'] = $localPath;
				$resources['remoteExtPath'] = 'TimedMediaHandler/MwEmbedModules/' . basename( $localPath );

				$resourceLoader->register(
					$name,
					new ResourceLoaderFileModule( $resources )
				);
			}
		}

		// Continue module processing
		return true;
	}
}
