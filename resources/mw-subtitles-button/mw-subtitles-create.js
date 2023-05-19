const TextTrackMenuItem = videojs.getComponent( 'TextTrackMenuItem' );

class MwCreateSubtitlesMenuItem extends TextTrackMenuItem {
	constructor( player, options ) {
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
		super( player, options );

		this.addClass( 'vjs-texttrack-create' );
		this.controlText( 'test' );
	}
	handleClick() {
		// eslint-disable-next-line no-underscore-dangle
		const videoEl = this.player_.el();
		const provider = videoEl.getAttribute( 'data-mwprovider' );
		const articlePath = encodeURI( 'TimedText:' + videoEl.getAttribute( 'data-mwtitle' ) );
		let link;

		if ( provider === 'wikimediacommons' ) {
			// Move into the config
			link = 'https://commons.wikimedia.org/wiki/' + articlePath;
		} else {
			link = mw.config.get( 'wgArticlePath' ).replace( '$1', articlePath );
		}
		window.open( link, '_blank' );
	}
}
videojs.registerComponent( 'MwCreateSubtitlesMenuItem', MwCreateSubtitlesMenuItem );

module.exports = MwCreateSubtitlesMenuItem;
