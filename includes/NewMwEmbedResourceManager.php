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

		// Look for special 'messages' => 'moduleFile' key and load all modules file messages:
		foreach ( $resourceList as $name => $resources ) {
			if ( isset( $resources['messageDir'] ) ) {
				$filename = $localResourcePath . '/' . $resources['messageDir'] . '/en.json';
				$resourceList[$name]['messages'] = self::readJSONFileMessageKeys( $filename );
			}
		}

		// Check for module config ( @@TODO support per-module config )
		$configPath = $localResourcePath . '/' . $moduleName . '.config.php';
		if ( is_file( $configPath ) ) {
			self::$moduleConfig = array_merge( self::$moduleConfig, include $configPath );
		}

		// Add the resource list into the module set with its provided path
		self::$moduleSet[$localResourcePath] = $resourceList;
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

	/**
	 * Read a JSON file containing localisation messages and returns the
	 * message keys in it.
	 * This is copied and adapted of LocalisationCache::readJSONFile().
	 *
	 * @param string $fileName Name of file to read
	 * @throws Exception if there is a syntax error in the JSON file
	 * @return array with a 'messages' key, or empty array if the file doesn't exist
	 */
	public static function readJSONFileMessageKeys( $fileName ) {
		if ( !is_readable( $fileName ) ) {
			return [];
		}

		$json = file_get_contents( $fileName );
		if ( $json === false ) {
			return [];
		}

		$data = FormatJson::decode( $json, true );
		if ( $data === null ) {
			throw new Exception( __METHOD__ . ": Invalid JSON file: $fileName" );
		}

		// Remove keys starting with '@', they're reserved for metadata and non-message data
		foreach ( $data as $key => $unused ) {
			if ( $key === '' || $key[0] === '@' ) {
				unset( $data[$key] );
			}
		}

		// Only array (message) keys needed.
		return array_keys( $data );
	}
}
