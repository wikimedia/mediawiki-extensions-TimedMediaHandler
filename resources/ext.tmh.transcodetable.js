/**
* Javascript to support transcode table on image page
*/
$(document).ready(function(){
	// Error link popup:
	$('.transcodestatus .errorlink').click(function(){
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
	})
})
