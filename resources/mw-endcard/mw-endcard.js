'use strict';
const Plugin = videojs.getPlugin( 'plugin' );

/**
 * @extends Plugin
 */
class EndCard extends Plugin {
	constructor( player, options ) {
		super( player, options );
		this.overlay = document.createElement( 'div' );
		this.container = document.createElement( 'div' );
		this.overlay.appendChild( this.container );
		this.api = new mw.Api();
		this.imageDetails = {};

		if ( options.customClass ) {
			// eslint-disable-next-line mediawiki/class-doc
			player.addClass( options.customClass );
		}

		// Add our ui element
		player.ready( () => {
			const title = player.options().mwTitle;

			if ( !title ) {
				return;
			}

			this.api.get( {
				action: 'query',
				prop: 'imageinfo',
				titles: title.getPrefixedText(),
				iiprop: 'url|size',
				uselang: 'content',
				formatversion: 2,
				maxage: 300,
				smaxage: 300
			} ).then( ( result ) => {
				if ( !result || !result.query || !result.query.pages || !result.query.pages[ 0 ] ) {
					return;
				}
				this.imageDetails = result.query.pages[ 0 ].imageinfo[ 0 ] || {};
				this.originalFilename = title.getFileNameTextWithoutExtension();
				this.originalFilepage = this.imageDetails.descriptionurl || '';
				if ( this.imageDetails.width === 0 ) {
					return;
				}
				this.buildContent();

				const controlBar = player.getChild( 'controlBar' );
				this.overlay.className = 'vjs-mw-endcard';

				controlBar.contentEl().parentNode.insertBefore(
					this.overlay, controlBar.contentEl()
				);
			} );
		} );

		player.on( 'ended', () => {
			this.overlay.classList.add( 'vjs-mw-endcard-visible' );
		} );

		player.on( 'userinactive', () => {
			if ( this.player.paused() ) {
				this.overlay.classList.add( 'vjs-mw-endcard-visible' );
			}
		} );

		player.on( [ 'play', 'seeked' ], () => {
			this.overlay.classList.remove( 'vjs-mw-endcard-visible' );
		} );

		player.on( 'useractive', () => {
			if ( !this.player.paused() ) {
				this.overlay.classList.remove( 'vjs-mw-endcard-visible' );
			}
		} );
	}

	buildContent() {
		this.container.appendChild( this.buildShareSection() );
		this.container.appendChild( this.buildEmbedSection() );
	}

	/**
	 * Helper to build a common widget container with a title and body.
	 *
	 * @private
	 * @param {string} titleMsgKey i18n message key for the widget title
	 * @param {string} widgetClass additional CSS class to add to the widget
	 * @return {{widget: HTMLDivElement, body: HTMLDivElement}}
	 */
	createWidget( titleMsgKey, widgetClass ) {
		const widget = document.createElement( 'div' );
		const body = document.createElement( 'div' );
		const title = document.createElement( 'h6' );
		// eslint-disable-next-line mediawiki/msg-doc
		title.textContent = mw.msg( titleMsgKey );
		widget.appendChild( title );
		widget.appendChild( body );
		widget.setAttribute( 'role', 'region' );
		widget.classList.add( 'tmh-widget' );

		if ( widgetClass ) {
			// eslint-disable-next-line mediawiki/class-doc
			widget.classList.add( widgetClass );
		}
		return { widget, body };
	}

	/**
	 * @private
	 * @param {string} content
	 * @return {Promise<boolean>} true if copy succeeded, false otherwise
	 */
	async copyToClipboard( content ) {
		// eslint-disable-next-line compat/compat
		const hasFeature = navigator.clipboard && 'writeText' in navigator.clipboard;
		if ( !hasFeature ) {
			mw.notify( mw.msg( 'videojs-endcard-copy-failed' ), { type: 'error' } );
			return false;
		}
		try {
			// eslint-disable-next-line compat/compat
			await navigator.clipboard.writeText( content );
		} catch ( e ) {
			mw.notify( mw.msg( 'videojs-endcard-copy-failed' ), { type: 'error' } );
			return false;
		}
		mw.notify( mw.msg( 'videojs-endcard-copied' ) );
		return true;
	}

	async triggerShareAction() {
		const shareData = {
			url: this.originalFilepage,
			title: this.originalFilename,
			files: []
		};

		if ( typeof navigator.canShare === 'function' && navigator.canShare( shareData ) ) {
			await navigator.share( shareData );
		} else {
			mw.notify( mw.msg( 'videojs-endcard-share-not-possible' ), { type: 'warning' } );
		}
	}

	buildShareSection() {
		const copyLinkButton = new OO.ui.ButtonWidget();
		copyLinkButton.setIcon( 'copy' )
			.setTitle( mw.msg( 'videojs-endcard-copy-link' ) )
			.setLabel( mw.msg( 'videojs-endcard-copy-link' ) )
			.setFlags( 'progressive' );
		copyLinkButton.on( 'click', () => this.copyToClipboard( this.originalFilepage ) );
		const shareLinkButton = new OO.ui.ButtonWidget();
		shareLinkButton.setIcon( 'share' )
			.setTitle( mw.msg( 'videojs-endcard-share' ) )
			.setLabel( mw.msg( 'videojs-endcard-share' ) )
			.setFlags( 'progressive' );
		if ( typeof navigator.canShare === 'function' ) {
			shareLinkButton.on( 'click', () => this.triggerShareAction() );
		}

		const { widget: linkWidget, body: linkWidgetBody } = this.createWidget( 'videojs-endcard-share-title', 'link-widget' );
		linkWidgetBody.appendChild( copyLinkButton.$element[ 0 ] );
		linkWidgetBody.appendChild( shareLinkButton.$element[ 0 ] );
		return linkWidget;
	}

	buildEmbedSection() {
		const mediaWidth = this.imageDetails.width || 0;
		const mediaHeight = this.imageDetails.height || 0;

		const url = new URL( this.originalFilepage, document.baseURI );
		const searchParams = new URLSearchParams( url.search );
		searchParams.append( 'embedplayer', 'true' );
		url.search = searchParams.toString();
		const embedUrl = url.toString();

		const embed = document.createElement( 'textarea' );
		// only values that are safe can be used directly here
		embed.value = `<iframe width="${ mediaWidth }" height="${ mediaHeight }" frameborder="0" loading="lazy" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; web-share" allowfullscreen src="${ embedUrl }" />`;
		embed.addEventListener( 'click', () => this.copyToClipboard( embed.value ) );

		const { widget: embedWidget, body: embedWidgetBody } = this.createWidget( 'videojs-endcard-embed-title', 'embed-widget' );

		const copyButton = new OO.ui.ButtonWidget();
		copyButton.setIcon( 'copy' )
			.setTitle( mw.msg( 'videojs-endcard-embed-copy' ) )
			.setLabel( mw.msg( 'videojs-endcard-embed-copy' ) )
			.setFlags( 'progressive' );
		copyButton.on( 'click', () => this.copyToClipboard( embed.value ) );

		embedWidgetBody.appendChild( embed );
		embedWidgetBody.appendChild( copyButton.$element[ 0 ] );
		return embedWidget;
	}
}

// register the plugin
videojs.registerPlugin( 'endCard', EndCard );

module.exports = EndCard;
