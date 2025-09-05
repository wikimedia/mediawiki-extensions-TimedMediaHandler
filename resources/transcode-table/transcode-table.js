/*!
* Javascript to support transcode table on image page
*/
$( () => {
	function resetPopup( event ) {
		const tKey = $( event.target ).attr( 'data-transcodekey' );
		const $message = $( [
			document.createTextNode( mw.msg( 'timedmedia-reset-explanation' ) ),
			document.createElement( 'br' ),
			document.createElement( 'br' ),
			document.createTextNode( mw.msg( 'timedmedia-reset-areyousure' ) )
		] );

		event.preventDefault();

		OO.ui.confirm( $message, {
			title: mw.msg( 'timedmedia-reset' ),
			actions: [
				{
					action: 'accept',
					label: mw.msg( 'timedmedia-reset-button-reset' ),
					flags: [ 'primary', 'destructive' ]
				},
				{
					action: 'cancel',
					label: mw.msg( 'timedmedia-reset-button-cancel' ),
					flags: 'safe'
				}
			]
		} ).then( ( confirmed ) => {
			if ( !confirmed ) {
				return;
			}
			const api = new mw.Api();
			api.postWithEditToken( {
				action: 'transcodereset',
				transcodekey: tKey,
				title: mw.config.get( 'wgPageName' ),
				errorformat: 'html'
			} ).then(
				() => {
					// Refresh the page
					location.reload();
				},
				( code, data ) => {
					let errorText;
					if ( data.errors ) {
						errorText = data.errors[ 0 ][ '*' ];
					} else {
						errorText = mw.msg( 'timedmedia-reset-error' );
					}
					OO.ui.alert( errorText, {
						actions: [
							{
								action: 'ok',
								label: mw.msg( 'timedmedia-reset-button-dismiss' ),
								flags: 'safe'
							}
						]
					} );
				}
			);
		} );
	}

	// eslint-disable-next-line no-jquery/no-global-selector
	$( '.mw-filepage-transcodereset a' ).on( 'click', resetPopup );

	function errorPopup( event ) {
		event.preventDefault();
		const error = $( event.target ).data( 'error' );
		const $pre = $( '<pre>' ).text( error );
		OO.ui.alert( $pre, {
			actions: [
				{
					action: 'ok',
					label: mw.msg( 'timedmedia-reset-button-dismiss' ),
					flags: 'safe'
				}
			],
			size: 'large'
		} );
	}

	// eslint-disable-next-line no-jquery/no-global-selector
	const $errorLink = $( '.mw-filepage-transcodestatus .mw-tmh-pseudo-error-link' );
	$errorLink.wrapInner( function () {
		const $this = $( this );
		return $( '<a>' ).attr( {
			href: '',
			title: $this.text(),
			'data-error': $this.attr( 'data-error' )
		} ).on( 'click', errorPopup );
	} );
} );
