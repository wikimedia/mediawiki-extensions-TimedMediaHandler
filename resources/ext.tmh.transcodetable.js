/**
* Javascript to support transcode table on image page
*/
( function ( mw, $ ) {
	$( document ).ready( function () {
		var errorPopup, $errorLink;

		errorPopup = function () {
			// pop up dialog
			mw.addDialog( {
				'width': '640',
				'height': '480',
				'title': $(this).attr( 'title' ),
				'content': $('<textarea />')
					.css( {
						'width':'99%',
						'height':'99%'
					} )
					.text( $(this).attr('data-error') )
			} )
			.css( 'overflow', 'hidden' );
			return false;
		};

		// Old version. Need to keep for a little while in case of cached pages.
		$( '.mw-filepage-transcodestatus .errorlink' ).click( errorPopup );
		// New version.
		$errorLink = $( '.mw-filepage-transcodestatus .mw-tmh-pseudo-error-link' );
		$errorLink.wrapInner( function () {
			var $this = $( this );
			return $( '<a />' ).attr( {
				href: '#',
				title: $this.text(),
				'data-error': $this.attr( 'data-error' )
			} ).click( errorPopup );
		} );

		// Reset transcode action:
		$( '.mw-filepage-transcodereset a' ).click( function () {
			var tKey = $( this ).attr( 'data-transcodekey' ),
			buttons = {};

			buttons[ mw.msg( 'mwe-ok' ) ] = function () {
				var api,
					_thisDialog = this,
					cancelBtn = {};

				// Only show cancel button while loading:
				cancelBtn[ mw.msg( 'mwe-cancel' ) ] = function () {
					$(this).dialog( 'close' );
				};
				$( _thisDialog ).dialog( 'option', 'buttons', cancelBtn );

				$( this ).loadingSpinner();

				api = new mw.Api();
				api.postWithEditToken( {
					'action' : 'transcodereset',
					'transcodekey' : tKey,
					'title' : mw.config.get('wgPageName')
				} ).done( function () {
					// Refresh the page
					location.reload();
				} ).fail( function ( code, data ) {
					if( data.error && data.error.info ){
						$( _thisDialog ).text( data.error.info );
					} else {
						$( _thisDialog ).text( mw.msg( 'timedmedia-reset-error' ) );
					}
					var okBtn = {};
					okBtn[ mw.msg('mwe-ok') ] = function() { $(this).dialog( 'close' ); };
					$( _thisDialog ).dialog( 'option', 'buttons', okBtn );
				} );
			};
			buttons[ mw.msg( 'mwe-cancel' ) ] = function () {
				$( this ).dialog( 'close' );
			};
			// pop up dialog
			mw.addDialog( {
				'width': '400',
				'height': '200',
				'title': mw.msg( 'timedmedia-reset' ),
				'content': mw.msg( 'timedmedia-reset-confirm' ),
				'buttons': buttons
			} )
			.css( 'overflow', 'hidden' );
			return false;
		});
	});
} )( mediaWiki, jQuery );
