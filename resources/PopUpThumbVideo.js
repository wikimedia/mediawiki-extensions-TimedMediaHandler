/**
* Simple script to add pop-up video dialog link support for video thumbnails 
*/
( function( mw, $ ) {
	
	$(document).ready(function(){
		$('.PopUpMediaTransform').each(function(){
			var _parent = this;					
			$(this).find('a').click( function(){
				var $video = $( unescape( $(_parent).attr('data-videopayload') ) );
				mw.addDialog({
					'width' : parseInt(  $video.css('width') ) + 35,
					'height' : parseInt(  $video.css('height') ) + 55,
					'title' : $video.attr('data-mwtitle'),
					'content' : $video
				})
				.find('video').embedPlayer();
				// don't follow file link
				return false; 
			});
		});
	});

} )( mediaWiki, jQuery );