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
