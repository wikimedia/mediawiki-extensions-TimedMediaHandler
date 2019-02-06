/**
 * A media element corresponding to a <video> element.
 *
 * It is implemented as a collection of mediaSource objects. The media sources
 * will be initialized from the <video> element, its child <source> elements,
 * and/or the ROE file referenced by the <video> element.
 *
 * @param {element}
 *      videoElement <video> element used for initialization.
 * @constructor
 */
/* global OGVCompat */
( function () {
	'use strict';

	var config = mw.config.get( 'wgTimedMediaHandler' );

	mw.MediaElement = function ( element ) {
		this.init( element );
	};

	mw.MediaElement.prototype = {

		// The array of mediaSource elements.
		sources: null,

		// flag for ROE data being added.
		addedROEData: false,

		// Selected mediaSource element.
		selectedSource: null,

		/**
		 * Media Element constructor
		 *
		 * Sets up a mediaElement from a provided top level "video" element adds any
		 * child sources that are found
		 *
		 * @param {Element}
		 *      videoElement Element that has src attribute or has children
		 *      source elements
		 */
		init: function ( videoElement ) {
			var self = this;
			mw.log( 'EmbedPlayer::mediaElement:init:' + videoElement.id );
			this.parentEmbedId = videoElement.id;
			this.sources = [];

			// Process the videoElement as a source element:
			if ( videoElement ) {
				if ( $( videoElement ).attr( 'src' ) ) {
					self.tryAddSource( videoElement );
				}
				// Process elements source children
				$( videoElement ).find( 'source,track' ).each( function () {
					self.tryAddSource( this );
				} );
			}
		},

		/**
		 * Updates the time request for all sources that have a standard time
		 * request argument (ie &t=start_time/end_time)
		 *
		 * @param {String}
		 *      startNpt Start time in npt format
		 * @param {String}
		 *      endNpt End time in npt format
		 */
		updateSourceTimes: function ( startNpt, endNpt ) {
			this.sources.forEach( function ( mediaSource ) {
				mediaSource.updateSrcTime( startNpt, endNpt );
			} );
		},

		/**
		 * Get Text tracks
		 */
		getTextTracks: function () {
			var textTracks = [];
			this.sources.forEach( function ( source ) {
				if ( source.nodeName === 'track' || ( source.mimeType && source.mimeType.indexOf( 'text/' ) !== -1 ) ) {
					textTracks.push( source );
				}
			} );
			return textTracks;
		},
		/**
		 * Returns the array of mediaSources of this element.
		 *
		 * @param {String}
		 *      [mimeFilter] Filter criteria for set of mediaSources to return
		 * @return {Array} mediaSource elements.
		 */
		getSources: function ( mimeFilter ) {
			var i,
				sourceSet = [];
			if ( !mimeFilter ) {
				return this.sources;
			}
			// Apply mime filter:
			for ( i = 0; i < this.sources.length; i++ ) {
				if (
					this.sources[ i ].mimeType &&
					this.sources[ i ].mimeType.indexOf( mimeFilter ) !== -1
				) {
					sourceSet.push( this.sources[ i ] );
				}
			}
			return sourceSet;
		},

		/**
		 * Selects a source by id
		 *
		 * @param {String}
		 *      sourceId Id of the source to select.
		 * @return {MediaSource} The selected mediaSource or null if not found
		 */
		getSourceById: function ( sourceId ) {
			var i;
			for ( i = 0; i < this.sources.length; i++ ) {
				if ( this.sources[ i ].id === sourceId ) {
					return this.sources[ i ];
				}
			}
			return null;
		},

		/**
		 * Selects a particular source for playback updating the "selectedSource"
		 *
		 * @param {Number}
		 *      index Index of source element to set as selectedSource
		 */
		setSourceByIndex: function ( index ) {
			var i,
				oldSrc = this.selectedSource.getSrc(),
				playableSources = this.getPlayableSources();
			mw.log( 'EmbedPlayer::mediaElement:selectSource: ' + index );
			for ( i = 0; i < playableSources.length; i++ ) {
				if ( i === index ) {
					this.selectedSource = playableSources[ i ];
					break;
				}
			}
			if ( oldSrc !== this.selectedSource.getSrc() ) {
				$( '#' + this.parentEmbedId ).trigger( 'SourceChange' );
			}
		},
		/**
		 * Sets a the selected source to passed in source object
		 * @param {Object} Source
		 */
		setSource: function ( source ) {
			var oldSrc = this.selectedSource.getSrc();
			this.selectedSource = source;
			if ( oldSrc !== this.selectedSource.getSrc() ) {
				$( '#' + this.parentEmbedId ).trigger( 'SourceChange' );
			}
		},

		/**
		 * Selects the default source via cookie preference, default marked, or by
		 * id order
		 */
		autoSelectSource: function () {
			var vndSources, desktopVdn, mobileVdn, bandwidthDelta, bandwidthTarget,
				player, namedSourceSet, useBogoSlow,
				codecPref, i, codec, minSizeDelta, isBogoSlow, displayWidth, sizeDelta,
				// FIXME: nativePlayableSources is unused
				// eslint-disable-next-line no-unused-vars
				nativePlayableSources,
				self = this,
				// Select the default source
				playableSources = this.getPlayableSources();

			mw.log( 'EmbedPlayer::mediaElement::autoSelectSource' );
			// Check if there are any playableSources
			if ( playableSources.length === 0 ) {
				return false;
			}
			function setSelectedSource( source ) {
				self.selectedSource = source;
				return self.selectedSource;
			}

			// Set via module driven preference:
			$( this ).trigger( 'onSelectSource', playableSources );

			if ( self.selectedSource ) {
				mw.log( 'MediaElement::autoSelectSource: Set via trigger::' + self.selectedSource.getTitle() );
				return self.selectedSource;
			}

			// Set via marked default:
			playableSources.forEach( function ( source ) {
				if ( source.markedDefault ) {
					mw.log( 'MediaElement::autoSelectSource: Set via marked default: ' + source.markedDefault );
					setSelectedSource( source );
				}
			} );

			// Set apple adaptive ( if available )
			vndSources = this.getPlayableSources( 'application/vnd.apple.mpegurl' );
			if ( vndSources.length && mw.EmbedTypes.getMediaPlayers().getMIMETypePlayers( 'application/vnd.apple.mpegurl' ).length ) {
				// Check for device flags:
				vndSources.forEach( function ( source ) {
				// Kaltura tags vdn sources with iphonenew
					if ( source.getFlavorId() && source.getFlavorId().toLowerCase() === 'iphonenew' ) {
						mobileVdn = source;
					} else {
						desktopVdn = source;
					}
				} );
				// NOTE: We really should not have two VDN sources the point of vdn is to be a set of adaptive streams.
				// This work around is a result of Kaltura HLS stream tagging
				if ( mw.isIphone() && mobileVdn ) {
					setSelectedSource( mobileVdn );
				} else if ( desktopVdn ) {
					setSelectedSource( desktopVdn );
				}
			}
			if ( this.selectedSource ) {
				mw.log( 'MediaElement::autoSelectSource: Set via Adaptive HLS: source flavor id:' + self.selectedSource.getFlavorId() + ' src: ' + self.selectedSource.getSrc() );
				return this.selectedSource;
			}

			// Set via user bandwidth pref will always set source to closest bandwidth allocation while not going over  EmbedPlayer.UserBandwidth
			if ( $.cookie( 'EmbedPlayer.UserBandwidth' ) ) {
				bandwidthDelta = 999999999;
				bandwidthTarget = $.cookie( 'EmbedPlayer.UserBandwidth' );
				playableSources.forEach( function ( source ) {
					if ( source.bandwidth ) {
					// Check if a native source ( takes president over bandwidth selection )
						player = mw.EmbedTypes.getMediaPlayers().defaultPlayer( source.mimeType );
						if ( !player || player.library !== 'Native' ) {
							// continue
							return;
						}

						if ( Math.abs( source.bandwidth - bandwidthTarget ) < bandwidthDelta ) {
							bandwidthDelta = Math.abs( source.bandwidth - bandwidthTarget );
							setSelectedSource( source );
						}
					}
				} );
			}

			if ( this.selectedSource ) {
				mw.log( 'MediaElement::autoSelectSource: Set via bandwidth prefrence: source ' + this.selectedSource.bandwidth + ' user: ' + $.cookie( 'EmbedPlayer.UserBandwidth' ) );
				return this.selectedSource;
			}

			// If we have at least one native source, throw out non-native sources
			// for size based source selection:
			nativePlayableSources = playableSources.filter( function ( source ) {
				var mimeType = source.mimeType,
					player = mw.EmbedTypes.getMediaPlayers().defaultPlayer( mimeType );
				return player && player.library === 'Native';
			} );

			// Prefer native playback ( and prefer WebM over ogg and h.264 )
			namedSourceSet = {};
			useBogoSlow = false; // use benchmark only for ogv.js
			playableSources.forEach( function ( source ) {
				var shortName,
					mimeType = source.mimeType,
					player = mw.EmbedTypes.getMediaPlayers().defaultPlayer( mimeType );
				if ( player && ( player.library === 'Native' || player.library === 'OgvJs' ) ) {
					switch ( player.id ) {
						case 'mp3Native':
							shortName = 'mp3';
							break;
						case 'aacNative':
							shortName = 'aac';
							break;
						case 'oggNative':
							shortName = 'ogg';
							break;
						case 'ogvJsPlayer':
							useBogoSlow = true;
							shortName = 'ogvjs';
							break;
						case 'webmNative':
							shortName = 'webm';
							break;
						case 'vp9Native':
							shortName = 'vp9';
							break;
						case 'h264Native':
							shortName = 'h264';
							break;
						case 'appleVdn':
							shortName = 'appleVdn';
							break;
					}
					if ( !namedSourceSet[ shortName ] ) {
						namedSourceSet[ shortName ] = [];
					}
					namedSourceSet[ shortName ].push( source );
				}
			} );

			codecPref = config[ 'EmbedPlayer.CodecPreference' ];

			if ( codecPref ) {
				for ( i = 0; i < codecPref.length; i++ ) {
					codec = codecPref[ i ];
					if ( !namedSourceSet[ codec ] ) {
						continue;
					}
					// select based on size:
					// Set via embed resolution closest to relative to display size
					minSizeDelta = null;

					// unless we're really slow...
					isBogoSlow = useBogoSlow && OGVCompat.isSlow();

					if ( this.parentEmbedId ) {
						displayWidth = $( '#' + this.parentEmbedId ).width();
						// eslint-disable-next-line no-loop-func
						namedSourceSet[ codec ].forEach( function ( source ) {
							if (
								( isBogoSlow && source.height > 240 ) ||
								( useBogoSlow && source.height > 360 )
							) {
								// On iOS or slow Windows devices, large videos decoded in JavaScript are a bad idea!
								// continue
								return;
							}
							if ( source.width && displayWidth ) {
								if ( source.width > displayWidth ) {
									// Bigger than the space to display?
									// Skip it unless it's the only one that fits.
									// continue
									return;
								}
								sizeDelta = Math.abs( source.width - displayWidth );
								mw.log( 'MediaElement::autoSelectSource: size delta : ' + sizeDelta + ' for s:' + source.width );
								if ( minSizeDelta === null || sizeDelta < minSizeDelta ) {
									minSizeDelta = sizeDelta;
									setSelectedSource( source );
								}
							}
							// Fall through to next one...
						} );
					}
					// If we found a source via display size return:
					if ( this.selectedSource ) {
						mw.log( 'MediaElement::autoSelectSource: from  ' + this.selectedSource.mimeType + ' because of resolution:' + this.selectedSource.width + ' close to: ' + displayWidth );
						return this.selectedSource;
					}
				// else fall through to defaults...
				}
			}

			// Set h264 via native or flash fallback
			playableSources.forEach( function ( source ) {
				var mimeType = source.mimeType,
					player = mw.EmbedTypes.getMediaPlayers().defaultPlayer( mimeType );
				if (
					mimeType === 'video/h264' &&
					player &&
					(
						player.library === 'Native' ||
						player.library === 'Kplayer'
					)
				) {
					if ( source ) {
						mw.log( 'MediaElement::autoSelectSource: Set h264 via native or flash fallback:' + source.getTitle() );
						setSelectedSource( source );
					}
				}
			} );

			// Else just select the first playable source
			if ( !this.selectedSource && playableSources[ 0 ] ) {
				mw.log( 'MediaElement::autoSelectSource: Set via first source: ' + playableSources[ 0 ].getTitle() + ' mime: ' + playableSources[ 0 ].getMIMEType() );
				return setSelectedSource( playableSources[ 0 ] );
			}
			// No Source found so no source selected
			return false;
		},

		/**
		 * check if the mime is ogg
		 */
		isOgg: function ( mimeType ) {
			return mimeType === 'video/ogg' ||
				mimeType === 'ogg/video' ||
				mimeType === 'video/annodex' ||
				mimeType === 'application/ogg';
		},

		/**
		 * Returns the thumbnail URL for the media element.
		 *
		 * @return {String} thumbnail URL
		 */
		getPosterSrc: function () {
			return this.poster;
		},

		/**
		 * Checks whether there is a stream of a specified MIME type.
		 *
		 * @param {string} mimeType MIME type to check.
		 * @return {boolean} true if sources include MIME false if not.
		 */
		hasStreamOfMIMEType: function ( mimeType ) {
			var i;
			for ( i = 0; i < this.sources.length; i++ ) {
				if ( this.sources[ i ].getMIMEType() === mimeType ) {
					return true;
				}
			}
			return false;
		},

		/**
		 * Checks if media is a playable type
		 */
		isPlayableType: function ( mimeType ) {
			// mw.log("isPlayableType:: " + mimeType);
			if ( mw.EmbedTypes.getMediaPlayers().defaultPlayer( mimeType ) ) {
				mw.log( 'isPlayableType:: ' + mimeType );
				return true;
			} else {
				return false;
			}
		},

		/**
		 * Adds a single mediaSource using the provided element if the element has a
		 * 'src' attribute.
		 *
		 * @param {Element}
		 *      element <video>, <source> or <mediaSource> <text> element.
		 */
		tryAddSource: function ( element ) {
			var i, source,
				newSrc = $( element ).attr( 'src' );

			// mw.log( 'mw.MediaElement::tryAddSource:' + $( element ).attr( "src" ) );

			if ( newSrc ) {
				// Make sure an existing element with the same src does not already exist:
				for ( i = 0; i < this.sources.length; i++ ) {
					if ( this.sources[ i ].src === newSrc ) {
						// Source already exists update any new attr:
						this.sources[ i ].updateSource( element );
						return this.sources[ i ];
					}
				}
			}
			// Create a new source
			source = new mw.MediaSource( element );

			this.sources.push( source );
			// mw.log( 'tryAddSource: added source ::' + source + 'sl:' + this.sources.length );
			return source;
		},

		/**
		 * Get playable sources
		 *
		 *@pram mimeFilter {=string} (optional) Filter the playable sources set by mime filter
		 *
		 * @return {Array} of playable media sources
		 */
		getPlayableSources: function ( mimeFilter ) {
			var i,
				playableSources = [];
			for ( i = 0; i < this.sources.length; i++ ) {
				if ( this.isPlayableType( this.sources[ i ].mimeType ) &&
					( !mimeFilter || this.sources[ i ].mimeType.indexOf( mimeFilter ) !== -1 )
				) {
					playableSources.push( this.sources[ i ] );
				}
			}
			mw.log( 'MediaElement::GetPlayableSources mimeFilter:' + mimeFilter + ' ' +
				playableSources.length + ' sources playable out of ' + this.sources.length );

			return playableSources;
		}
	};

}() );
