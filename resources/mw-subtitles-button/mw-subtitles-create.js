/* eslint-disable no-implicit-globals */
var TextTrackMenuItem = videojs.getComponent( 'TextTrackMenuItem' );

var MwCreateSubtitlesMenuItem = videojs.extend( TextTrackMenuItem, {
	constructor: function ( player, options ) {
		options.track = {
			player: player,
			kind: options.kind,
			label: 'Create ' + options.kind,
			selectable: false,
			default: false,
			mode: 'disabled'
		};
		options.selectable = false;
		options.name = 'MwCreateSubtitlesMenuItem';

		TextTrackMenuItem.call( this, player, options );
		this.addClass( 'vjs-texttrack-create' );
		this.controlText( 'test' );
	},
	handleClick: function () {
		// eslint-disable-next-line no-underscore-dangle
		var videoEl = this.player_.el();
		var provider = videoEl.getAttribute( 'data-mwprovider' );
		var articlePath = encodeURI( 'TimedText:' + videoEl.getAttribute( 'data-mwtitle' ) );
		var link;

		if ( provider === 'wikimediacommons' ) {
			// Move into the config
			link = 'https://commons.wikimedia.org/wiki/' + articlePath;
		} else {
			link = mw.config.get( 'wgArticlePath' ).replace( '$1', articlePath );
		}
		window.open( link, '_blank' );
	}
} );
videojs.registerComponent( 'MwCreateSubtitlesMenuItem', MwCreateSubtitlesMenuItem );

module.exports = MwCreateSubtitlesMenuItem;
