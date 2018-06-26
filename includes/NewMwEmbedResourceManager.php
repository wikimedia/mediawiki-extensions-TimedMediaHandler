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
	 * @param string $mwEmbedResourcePath
	 * @throws Exception
	 */
	public static function register( $mwEmbedResourcePath ) {
		// @codingStandardsIgnoreStart
		global $IP;
		// @codingStandardsIgnoreEnd
		$localResourcePath = $IP . '/' . $mwEmbedResourcePath;
		// Get the module name from the end of the path:
		$modulePathParts = explode( '/', $mwEmbedResourcePath );
		$moduleName = array_pop( $modulePathParts );
		if ( !is_dir( $localResourcePath ) ) {
			throw new Exception(
				__METHOD__ . " not given readable path: " . htmlspecialchars( $localResourcePath )
			);
		}

		if ( substr( $mwEmbedResourcePath, -1 ) == '/' ) {
			throw new Exception(
				__METHOD__ . " path has trailing slash: " . htmlspecialchars( $localResourcePath )
			);
		}

		// Check that resource file is present:
		$resourceListFilePath = $localResourcePath . '/' . $moduleName . '.php';
		if ( !is_file( $resourceListFilePath ) ) {
			throw new Exception(
				__METHOD__ . " mwEmbed Module is missing resource list: " . htmlspecialchars(
					$resourceListFilePath
				)
			);
		}
		// Get the mwEmbed module resource registration:
		$resourceList = include $resourceListFilePath;

		// Look for special 'messages' => 'moduleFile' key and load all modules file messages:
		foreach ( $resourceList as $name => $resources ) {
			if (
				isset( $resources['messageFile'] ) &&
				is_file( $localResourcePath . '/' . $resources['messageFile'] )
			) {
				$resourceList[$name]['messages'] = [];
				include $localResourcePath . '/' . $resources['messageFile'];
				foreach ( $messages['en'] as $msgKey => $na ) {
					$resourceList[$name]['messages'][] = $msgKey;
				}
			}

			if ( isset( $resources['messageDir'] ) ) {
				$filename = $localResourcePath . '/' . $resources['messageDir'] . '/en.json';
				$resourceList[$name]['messages'] = self::readJSONFileMessageKeys( $filename );
			}
		};

		// Check for module loader:
		if ( is_file( $localResourcePath . '/' . $moduleName . '.loader.js' ) ) {
			$resourceList['mw.' . $moduleName . '.loader'] = [
				'scripts' => $moduleName . '.loader.js',
				'position' => 'top',
			];
		}

		// Check for module config ( @@TODO support per-module config )
		$configPath = $localResourcePath . '/' . $moduleName . '.config.php';
		if ( is_file( $configPath ) ) {
			self::$moduleConfig = array_merge( self::$moduleConfig, include $configPath );
		}

		// Add the resource list into the module set with its provided path
		self::$moduleSet[$mwEmbedResourcePath] = $resourceList;
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
		// @codingStandardsIgnoreStart
		global $IP;
		// @codingStandardsIgnoreEnd
		global $wgScriptPath;
		// Register all the resources with the resource loader
		foreach ( self::$moduleSet as $path => $modules ) {
			// remove 'extension' prefix from path
			$remoteExtPath = explode( '/', $path );
			array_shift( $remoteExtPath );
			$remoteExtPath = implode( '/', $remoteExtPath );
			foreach ( $modules as $name => $resources ) {
				$resources['remoteExtPath'] = $remoteExtPath;

				// Running as mediawiki extension, so ResourceLoaderFileModule is used
				// to add $wgScriptPath to path
				$resourcePath = $wgScriptPath . '/' . $path;
				$resourceLoader->register(
					$name, new ResourceLoaderFileModule(
						$resources, "$IP/$path", $resourcePath
					)
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
