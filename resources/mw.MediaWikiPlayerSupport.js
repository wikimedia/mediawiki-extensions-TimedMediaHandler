( function ( mw, $ ) {
	/**
	 * Merge in the default video attributes supported by embedPlayer:
	 */
	mw.mergeConfig( 'EmbedPlayer.Attributes', {
		// A apiTitleKey for looking up subtitles, credits and related videos
		'data-mwtitle': null,

		// The apiProvider where to lookup the title key
		'data-mwprovider': null
	} );

	// Add mediaWiki player support to target embedPlayer
	$( mw ).on( 'EmbedPlayerNewPlayer', function ( event, embedPlayer ) {
		mw.addMediaWikiPlayerSupport( embedPlayer );
	} );

	/**
	 * Closure function wraps mediaWiki embedPlayer bindings
	 *
	 * @param {Object} embedPlayer
	 */
	mw.addMediaWikiPlayerSupport = function ( embedPlayer ) {
		var apiTitleKey, apiProvider, $creditsCache = false;
		// Set some local variables:
		if ( !embedPlayer[ 'data-mwtitle' ] ) {
			return;
		} else {
			apiTitleKey = embedPlayer[ 'data-mwtitle' ];
			// legacy support ( set as attribute )
			embedPlayer.apiTitleKey = apiTitleKey;
		}
		// Set local apiProvider via config if not defined
		apiProvider = embedPlayer[ 'data-mwprovider' ];
		if ( !apiProvider ) {
			apiProvider = mw.config.get( 'EmbedPlayer.ApiProvider' );
		}

		/*!
		 * Loads mediaWiki sources for a given embedPlayer
		 *
		 * @param {function} callback Function called once player sources have been added
		 */
		function loadPlayerSources( callback ) {
			// Setup the request
			var request = {
				prop: 'imageinfo',
				// In case the user added File: or Image: to the apiKey:
				titles: 'File:' + decodeURIComponent( apiTitleKey ).replace( /^(File:|Image:)/, '' ),
				iiprop: 'url|size|dimensions|metadata',
				iiurlwidth: embedPlayer.getWidth(),
				redirects: true // automatically resolve redirects
			};

			// Run the request:
			mw.getJSON( mw.getApiProviderURL( apiProvider ), request, function ( data ) {
				var i, page, imageinfo;
				if ( data.query.pages ) {
					for ( i in data.query.pages ) {
						if ( i === '-1' ) {
							callback( false );
							return;
						}
						page = data.query.pages[ i ];
					}
				} else {
					callback( false );
					return;
				}
				// Make sure we have imageinfo:
				if ( !page.imageinfo || !page.imageinfo[ 0 ] ) {
					callback( false );
					return;
				}
				imageinfo = page.imageinfo[ 0 ];

				// TODO these should call public methods rather than update internals:

				// Update the poster
				embedPlayer.poster = imageinfo.thumburl;

				// Add the media src
				embedPlayer.mediaElement.tryAddSource(
					$( '<source>' )
						.attr( 'src', imageinfo.url )
						.get( 0 )
				);

				// Set the duration
				if ( imageinfo.metadata[ 2 ].name === 'length' ) {
					embedPlayer.duration = imageinfo.metadata[ 2 ].value;
				}

				// Set the width height
				// Make sure we have an accurate aspect ratio
				if ( imageinfo.height !== 0 && imageinfo.width !== 0 ) {
					embedPlayer.height = Math.floor( embedPlayer.width * ( imageinfo.height / imageinfo.width ) );
				}

				// Update the css for the player interface
				$( embedPlayer ).css( 'height', embedPlayer.height );

				callback();
			} );
		}

		/*!
		* Build a clip credit from the resource wikiText page
		*
		* TODO parse the resource page template
		*
		* @param {String} resourceHTML Resource wiki text page contents
		*/
		function doCreditLine( resourceHTML, articleUrl ) {
			var authUrl, $page, $author, $authorText, $links, $date, $authorLink,
				imgSize = {},
				// Get the title string ( again a "Title" like js object could help out here. )
				titleStr = embedPlayer.apiTitleKey.replace( /_/g, ' ' ),
				// Setup the initial credits line:
				$creditLine = $( '<div>' );

			// Add the title:
			$creditLine.append(
				$( '<span>' ).html(
					mw.msg( 'mwe-embedplayer-credit-title',
						// get the link
						$( '<div>' ).append(
							$( '<a/>' )
								.attr( {
									href: articleUrl,
									title: titleStr
								} )
								.text( titleStr )
						)[ 0 ].innerHTML
					)
				)
			);

			// Parse some data from the page info template if possible:
			$page = $( resourceHTML );

			// Look for author:
			$author = $page.find( '#fileinfotpl_aut' );
			if ( $author.length ) {
				// Get the real author sibling of fileinfotpl_aut
				$authorText = $author.next();
				// Remove white space:
				$authorText.find( 'br' ).remove();

				// Update link to be absolute per page url context:
				$links = $authorText.find( 'a' );
				if ( $links.length ) {
					$links.each( function ( i, authorLink ) {
						$authorLink = $( authorLink );
						authUrl = $authorLink.attr( 'href' );
						authUrl = mw.absoluteUrl( authUrl, articleUrl );
						$authorLink.attr( 'href', authUrl );
					} );
				}
				$creditLine.append( $( '<br>' ),
					mw.msg( 'mwe-embedplayer-credit-author', $authorText.html() )
				);
			}

			// Look for date:
			$date = $page.find( '#fileinfotpl_date' );
			if ( $date.length ) {
				// Get the real date sibling of fileinfotpl_date
				$date = $date.next();

				// remove white space:
				$date.find( 'br' ).remove();
				$creditLine.append( $( '<br>' ),
					mw.msg( 'mwe-embedplayer-credit-date', $date.html() )
				);
			}

			// Build out the image and credit line
			if ( embedPlayer.isAudio() ) {
				imgSize.height = imgSize.width = ( embedPlayer.controlBuilder.getOverlayWidth() < 250 ) ? 45 : 80;
			} else {
				imgSize.width = ( embedPlayer.controlBuilder.getOverlayWidth() < 250 ) ? 45 : 120;
				imgSize.height = Math.floor( imgSize.width * ( embedPlayer.getHeight() / embedPlayer.getWidth() ) );
			}
			return $( '<div/>' ).addClass( 'creditline' )
				.append(
					$( '<a/>' ).attr( {
						href: articleUrl,
						title: titleStr
					} ).html(
						$( '<img/>' ).attr( {
							border: 0,
							src: embedPlayer.poster
						} ).css( imgSize )
					)
				)
				.append(
					$creditLine
				);
		}

		/**
		 * Issues a request to populate the credits box
		 *
		 * @param {jQuery} $target
		 * @param {Function} callback
		 */
		function showCredits( $target, callback ) {
			var apiUrl, fileTitle, request;
			if ( $creditsCache ) {
				$target.html( $creditsCache );
				callback( true );
				return;
			}
			// Setup shortcuts:
			apiUrl = mw.getApiProviderURL( apiProvider );
			fileTitle = 'File:' + decodeURIComponent( apiTitleKey ).replace( /^File:|^Image:/, '' );

			// Get the image page ( cache for 1 hour )
			request = {
				action: 'parse',
				page: fileTitle,
				smaxage: 3600,
				maxage: 3600
			};
			mw.getJSON( apiUrl, request, function ( data ) {
				var descUrl = apiUrl.replace( 'api.php', 'index.php' );
				descUrl += '?title=' + encodeURIComponent( fileTitle );
				if ( data && data.parse && data.parse.text && data.parse.text[ '*' ] ) {
					// TODO improve provider 'concept' to support page title link
					$creditsCache = doCreditLine( data.parse.text[ '*' ], descUrl );
				} else {
					$creditsCache = doCreditLine( false, descUrl );
				}
				$target.html( $creditsCache );
				callback( true );
			} );
		}
		/**
		 * Adds embedPlayer Bindings
		 */

		// Show credits when requested
		$( embedPlayer ).bindQueueCallback( 'showCredits', function ( $target, callback ) {
			if ( $target.data( 'playerId' ) !== embedPlayer.id ) {
				// bad event trigger
				return;
			}
			// Only request the credits once:
			showCredits( $target, callback );
		} );

		// Show credits on clip complete:
		$( embedPlayer ).on( 'onEndedDone', function ( event, id ) {
			var cb;
			if ( embedPlayer.id !== id ) {
				// possible event trigger error. ( skip )
				return;
			}
			// dont show credits for audio elements,
			// seek to begining instead
			if ( embedPlayer.isAudio() ) {
				embedPlayer.setCurrentTime( 0 );
				return;
			}
			cb = embedPlayer.controlBuilder;
			cb.checkMenuOverlay();
			cb.showMenuOverlay();
			cb.showMenuItem( 'credits' );
		} );

		$( embedPlayer ).on( 'showInlineDownloadLink', function () {
			// Add recommend HTML5 player if we have non-native playback:
			if ( embedPlayer.controlBuilder.checkNativeWarning() ) {
				embedPlayer.controlBuilder.addWarningBinding(
					'EmbedPlayer.ShowNativeWarning',
					mw.msg( 'mwe-embedplayer-for_best_experience',
						$( '<div>' ).append(
							$( '<a>' ).attr( {
								href: 'https://www.mediawiki.org/wiki/Extension:TimedMediaHandler/Client_download',
								target: '_new'
							} )
						)[ 0 ].innerHTML
					),
					true
				);
			}
		} );

		$( embedPlayer ).on( 'TimedText_BuildCCMenu', function ( event, $menu, id ) {
			var thisep,
				pageTitle,
				addTextPage,
				$li;
			if ( id !== embedPlayer.id ) {
				thisep = $( '#' + id )[ 0 ].timedText;
				embedPlayer = thisep.embedPlayer;
			}
			// Put in the "Make Transcript" link if config enabled and we have an api key
			if ( embedPlayer.apiTitleKey ) {
				// check if not already there:
				if ( $menu.find( '.add-timed-text' ).length ) {
					// add text link already present
					return;
				}

				pageTitle = 'TimedText:' +
					decodeURIComponent( embedPlayer.apiTitleKey ).replace( /^File:|^Image:/, '' );
				addTextPage = mw.getApiProviderURL( apiProvider ).replace( 'api.php', 'index.php' ) +
					'?title=' + encodeURIComponent( pageTitle );

				$li = $.getLineItem( mw.msg( 'mwe-timedtext-upload-timed-text' ), 'script', function () {
					window.location = addTextPage;
				} );

				$li.addClass( 'add-timed-text' )
					.find( 'a' )
					.attr( {
						href: addTextPage,
						target: '_new'
					} );
				$menu.append(
					$li
				);
			}
		} );

		$( embedPlayer ).bindQueueCallback( 'checkPlayerSourcesEvent', function ( callback ) {
			// Only load source if none are available:
			if ( embedPlayer.mediaElement.sources.length === 0 ) {
				loadPlayerSources( callback );
			} else {
				// No source to load, issue callback directly
				callback();
			}
		} );
		$( mw ).bindQueueCallback( 'TimedText_LoadTextSource', function ( source, callback ) {
			var apiUrl, request;
			if ( !source.mwtitle || !source.mwprovider ) {
				callback();
				return;
			}
			// Load via api
			apiUrl = mw.getApiProviderURL( source.mwprovider );
			// Get the image page ( cache for 1 hour )
			request = {
				action: 'parse',
				page: source.mwtitle,
				smaxage: 3600,
				maxage: 3600
			};
			mw.getJSON( apiUrl, request, function ( data ) {
				if ( data && data.parse && data.parse.text && data.parse.text[ '*' ] ) {
					source.loaded = true;
					source.mimeType = 'text/mw-srt';
					source.captions = source.getCaptions( data.parse.text[ '*' ] );
					callback();
				} else {
					mw.log( 'Error: MediaWiki api error in getting timed text:', data );
					callback();
				}
			} );
		} );

		$( embedPlayer ).on( 'getShareIframeSrc', function ( event, callback, id ) {
			var iframeUrl = false;
			if ( id !== embedPlayer.id ) {
				embedPlayer = $( '#' + id )[ 0 ];
			}
			// Do a special check for wikimediacommons provider as a known shared reop
			if ( embedPlayer[ 'data-mwprovider' ] === 'wikimediacommons' ) {
				iframeUrl = '//commons.wikimedia.org/wiki/File:' + decodeURIComponent( embedPlayer.apiTitleKey ).replace( /^(File:|Image:)/, '' );
			} else {
				// use the local wiki:
				if ( mw.config.get( 'wgServer' ) && mw.config.get( 'wgArticlePath' ) ) {
					iframeUrl = mw.config.get( 'wgServer' ) +
						mw.config.get( 'wgArticlePath' ).replace( /\$1/, 'File:' +
							decodeURIComponent( embedPlayer.apiTitleKey ).replace( /^(File:|Image:)/, '' ) );
				}
			}
			if ( iframeUrl ) {
				iframeUrl += '?embedplayer=yes';
			}
			callback( iframeUrl );
		} );
	};

}( mediaWiki, jQuery ) );
