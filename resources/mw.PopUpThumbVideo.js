/**
* Simple script to add pop-up video dialog link support for video thumbnails 
*/
( function( mw, $ ) {
	
	$(document).ready(function(){
		$('.PopUpMediaTransform').each(function(){
			var _parent = this;					
			$( this ).find('a').click( function(){
				var $video = $( unescape( $(_parent).attr('data-videopayload') ) );
				mw.addDialog( {
					'width' : parseInt(  $video.css('width') ) + 20,
					'height' : parseInt(  $video.css('height') ) + 45,
					'title' : $video.attr('data-mwtitle'),
					'content' : $video,
					'close' : function(){
						// pause the video on close ( so that playback does not continue )
						var domEl = $(this).find('video,audio').get(0);
						if( domEl ) {
							domEl.pause();
						}
					}
				} )
				.css('overflow', 'hidden')
				.find('video,audio').embedPlayer();				
				// don't follow file link
				return false; 
			});
		});
	});

} )( mediaWiki, jQuery );
