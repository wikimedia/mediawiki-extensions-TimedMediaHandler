/**
* Javascript to support transcode table on image page
*/
$(document).ready(function(){
	var errorPopup, $errorLink;

	errorPopup = function () {
		// pop up dialog
		mw.addDialog({
			'width' : '640',
			'height' : '480',
			'title' : $(this).attr('title'),
			'content' : $('<textarea />')
				.css({
					'width':'99%',
					'height':'99%'
				})
				.text( $(this).attr('data-error') )
		})
		.css('overflow', 'hidden');
		return false;
	}

	// Old version. Need to keep for a little while in case of cached pages.
	$( '.mw-filepage-transcodestatus .errorlink' ).click( errorPopup );
	// New version.
	$errorLink = $( '.mw-filepage-transcodestatus .mw-tmh-pseudo-error-link' );
	$errorLink.wrapInner(
		$( '<a />' ).attr( {
			href: '#',
			title: $errorLink.text(),
			'data-error': $errorLink.attr('data-error')
		} ).click( errorPopup )
	);

	// Reset transcode action:
	$('.mw-filepage-transcodereset a').click( function(){
		var tKey = $(this).attr('data-transcodekey');
		var buttons = {};
		buttons[ mw.msg('mwe-ok') ] = function(){
			var _thisDialog = this;

			// Only show cancel button while loading:
			var cancelBtn = {};
			cancelBtn[ mw.msg('mwe-cancel') ] = function() {
				$(this).dialog("close");
			}
			$( _thisDialog ).dialog( "option", "buttons", cancelBtn );

			$( this ).loadingSpinner();

			var apiUrl =  mw.config.get('wgServer') + mw.config.get( 'wgScriptPath' ) + '/api.php';
			// Do an api post action:
			$.post( apiUrl, {
				'action' : 'transcodereset',
				'transcodekey' : tKey,
				'title' : mw.config.get('wgPageName'),
				'token' : mw.user.tokens.get('editToken'),
				'format' : 'json'
			}, function( data ){
				if( data && data['success'] ){
					// refresh the page
					window.location.reload();
				} else {
					if( data.error && data.error.info ){
						$( _thisDialog ).text( data.error.info );
					} else {
						$( _thisDialog ).text( mw.msg( 'timedmedia-reset-error' ) );
					}
					var okBtn = {};
					okBtn[ mw.msg('mwe-ok') ] = function() { $(this).dialog("close"); }
					$( _thisDialog ).dialog( "option", "buttons", okBtn );
				}
			})
		};
		buttons[ mw.msg('mwe-cancel') ] =function(){
			$(this).dialog('close');
		}
		// pop up dialog
		mw.addDialog({
			'width' : '400',
			'height' : '200',
			'title' : mw.msg('timedmedia-reset'),
			'content' : mw.msg('timedmedia-reset-confirm'),
			'buttons': buttons
		})
		.css('overflow', 'hidden');
		return false;
	})
})
