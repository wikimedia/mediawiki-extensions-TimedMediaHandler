( function ( mw ) {
	/**
	 * makeAbsolute takes makes the given
	 * document.URL or a contextUrl param
	 *
	 * protocol relative urls are prepended with http or https
	 *
	 * @param {string} source Path or url
	 * @param {string} contextUrl The domain / context for creating an absolute url from a relative path
	 * @return {string} Absolute url
	 */
	mw.absoluteUrl = function ( source, contextUrl ) {
		var contextUrlObj, fileUrl;
		// check if the url is already absolute:
		if ( source.indexOf( 'http://' ) === 0 || source.indexOf( 'https://' ) === 0 ) {
			return source;
		}

		// Get parent Url location the context URL
		contextUrlObj = new mw.Uri( contextUrl || document.URL );

		if ( source.indexOf( '//' ) === 0 ) {
			return contextUrlObj.protocol + ':' + source;
		}

		// Check for local windows file that does not flip the slashes:
		if ( contextUrlObj.directory === '' && contextUrlObj.protocol === 'file' ) {
			// pop off the file
			fileUrl = contextUrlObj.split( '\\' );
			fileUrl.pop();
			return fileUrl.join( '\\' ) + '\\' + source;
		}
		// Check for leading slash:
		if ( source.indexOf( '/' ) === 0 ) {
			return contextUrlObj.protocol + '://' + contextUrlObj.getAuthority() + source;
		} else {
			return contextUrlObj.protocol + '://' + contextUrlObj.getAuthority() + contextUrlObj.path + source;
		}
	};

}( mediaWiki ) );
