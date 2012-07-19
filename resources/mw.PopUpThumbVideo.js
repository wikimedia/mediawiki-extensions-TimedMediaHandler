/**
* Simple script to add pop-up video dialog link support for video thumbnails
*/
( function( mw, $ ) {
	$(document).ready(function(){
		$('.PopUpMediaTransform a').each( function(){
			$( this ).click( function( event ){
				var $videoContainer = $( unescape( $(this).parent().attr('data-videopayload') ) );
				mw.addDialog({
					'width' : 'auto',
					'height' : 'auto',
					'title' : $videoContainer.find('video,audio').attr('data-mwtitle'),
					'content' : $videoContainer,
					'close' : function(){
						// On close pause the video on close ( so that playback does not continue )
						var domEl = $(this).find('video,audio').get(0);
						if( domEl && domEl.pause ) {
							domEl.pause();
						}
						return true;
					}
				})
				.css('overflow', 'hidden')
				.find('video,audio').embedPlayer();
				// don't follow file link
				return false;
			});
		});
	});

} )( mediaWiki, jQuery );
