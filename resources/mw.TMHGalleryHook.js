/**
 * Simple script to add pop-up video dialog link support for video thumbnails
 */
( function ( mw ) {
	// Hook to allow dynamically resizing videos in image galleries
	mw.hook( 'mediawiki.page.gallery.resize' ).add( function ( info ) {
		var $mwPlayerContainer,
			$popUp,
			$tmhVideo,
			$mwContainer = info.$imageDiv.find( '.mediaContainer' );
		if ( info.resolved ) {
			// Everything is already done here.
			return;
		}

		$mwContainer = info.$imageDiv.find( '.mediaContainer' );
		if ( $mwContainer.length ) {
			// Add some padding, so caption doesn't overlap video controls if
			// we are overlaying the caption on top of the image.
			if ( !info.$outerDiv.parent().hasClass( 'mw-gallery-packed' ) ) {
				info.$outerDiv.find( 'div.gallerytext' ).css( 'padding-bottom', '20px' );
			}

			info.$imageDiv.width( info.imgWidth );
			$mwContainer.width( info.imgWidth );
			$mwPlayerContainer = $mwContainer.children( '.mwPlayerContainer' );
			if ( $mwPlayerContainer.length ) {
				// Case 1: HTML5 player already loaded
				$mwPlayerContainer.width( info.imgWidth ).height( info.imgHeight );
				$mwPlayerContainer.find( 'img.playerPoster' ).width( info.imgWidth ).height( info.imgHeight );
			} else {
				// Case 2: Raw video element
				$tmhVideo = info.$imageDiv.find( 'video' );
				$tmhVideo.width( info.imgWidth ).height( info.imgHeight );
			}
			info.resolved = true;
			return;
		}

		$popUp = info.$imageDiv.find( '.PopUpMediaTransform' );
		if ( $popUp.length ) {
			info.$imageDiv.width( info.imgWidth );
			$popUp.add( $popUp.find( 'img' ) ).width( info.imgWidth ).height( info.imgHeight );
		}
	} );
}( mediaWiki ) );
