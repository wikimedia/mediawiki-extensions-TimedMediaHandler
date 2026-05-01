'use strict';

const Plugin = videojs.getPlugin( 'plugin' );

const RATES = [ 0.25, 0.5, 0.7, 0.8, 0.9, 1, 1.1, 1.2, 1.3, 1.5, 2 ];

// Assumed frame duration when media does not expose frame rate
const FALLBACK_FRAME_DURATION = 1 / 24;

class MwHotkeys extends Plugin {
	constructor( player, options ) {
		super( player, options );
		this.onKeyDown = this.onKeyDown.bind( this );
		player.on( 'keydown', this.onKeyDown );
	}

	onKeyDown( e ) {
		const key = e.key.length === 1 ? e.key.toLowerCase() : e.key;
		switch ( key ) {
			case ',':
				e.preventDefault();
				if ( this.player.paused() ) {
					this.stepFrame( -1 );
				}
				break;
			case '.':
				e.preventDefault();
				if ( this.player.paused() ) {
					this.stepFrame( 1 );
				}
				break;
			case '<':
				e.preventDefault();
				this.stepRate( -1 );
				break;
			case '>':
				e.preventDefault();
				this.stepRate( 1 );
				break;
			case '[':
				e.preventDefault();
				this.stepRate( -1 );
				break;
			case ']':
				e.preventDefault();
				this.stepRate( 1 );
				break;
			case 'j':
				e.preventDefault();
				this.seekBy( -10 );
				break;
			case 'l':
				e.preventDefault();
				this.seekBy( 10 );
				break;
			case 'ArrowLeft':
				e.preventDefault();
				this.seekBy( -5 );
				break;
			case 'ArrowRight':
				e.preventDefault();
				this.seekBy( 5 );
				break;
			case 'Home':
				e.preventDefault();
				this.player.currentTime( 0 );
				break;
			case 'End':
				e.preventDefault();
				this.player.currentTime( this.player.duration() );
				break;
			case 'c':
			case 's':
				e.preventDefault();
				this.toggleTextTrackKind();
				break;
		}
	}

	seekBy( seconds ) {
		const duration = this.player.duration();
		const next = Math.max( 0, Math.min( duration || 0, this.player.currentTime() + seconds ) );
		this.player.currentTime( next );
		this.player.trigger( { type: 'mwSeek', seconds: seconds } );
		if ( this.player.paused() ) {
			this.player.play();
		}
	}

	stepFrame( direction ) {
		const next = Math.max( 0, this.player.currentTime() + direction * FALLBACK_FRAME_DURATION );
		this.player.currentTime( next );
		this.player.trigger( { type: 'mwFrameStep', direction: direction } );
	}

	toggleTextTrackKind() {
		const kind = 'subtitles';
		const tracks = Array.from( this.player.textTracks() ).filter( ( t ) => t.kind === kind );
		const showing = tracks.some( ( t ) => t.mode === 'showing' );
		if ( showing ) {
			this.lastTrack = this.lastTrack || {};
			this.lastTrack[ kind ] = tracks.find( ( t ) => t.mode === 'showing' );
			tracks.forEach( ( t ) => {
				t.mode = 'hidden';
			} );
		} else {
			const restore = this.lastTrack && this.lastTrack[ kind ];
			const target = restore || tracks[ 0 ];
			if ( target ) {
				target.mode = 'showing';
			}
		}
	}

	stepRate( direction ) {
		const current = this.player.playbackRate();
		let newRate;

		if ( current > 2 ) {
			// Integer steps above 2; stepping down rejoins the fixed list at 2.
			newRate = Math.max( 2, Math.round( current ) + direction );
		} else {
			const idx = RATES.indexOf( current );
			if ( idx === -1 ) {
				// Non-standard rate: snap to adjacent value
				const clampedIdx = Math.max( 0, Math.min(
					RATES.length - 1,
					RATES.indexOf( 1 ) + direction
				) );
				newRate = RATES[ clampedIdx ];
			} else if ( idx === RATES.length - 1 && direction > 0 ) {
				// Switch above 2 logic
				newRate = 3;
			} else {
				newRate = RATES[ Math.max( 0, idx + direction ) ];
			}
		}

		this.player.playbackRate( newRate );
	}

	dispose() {
		this.player.off( 'keydown', this.onKeyDown );
		super.dispose();
	}
}

videojs.registerPlugin( 'mwHotkeys', MwHotkeys );
module.exports = MwHotkeys;
