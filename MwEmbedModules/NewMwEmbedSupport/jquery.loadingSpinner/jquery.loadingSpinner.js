/* global Spinner */

( function ( mw, $ ) {
	/**
	 * Set a given selector html to the loading spinner:
	 *
	 * @param {Object} opts
	 * @return {jQuery}
	 */
	$.fn.loadingSpinner = function ( opts ) {
		// empty the target:
		$( this ).empty();

		// If we have loader path defined, load an image
		if ( mw.config.get( 'LoadingSpinner.ImageUrl' ) ) {
			this.each( function () {
				var $loadingSpinner,
					$this = $( this ).empty();

				if ( $this.data( 'spinner' ) ) {
					$this.data( 'spinner', null );
				}
				if ( opts !== false ) {
					$loadingSpinner = $( '<img />' ).attr( 'src', mw.config.get( 'LoadingSpinner.ImageUrl' ) ).load( function () {
						// Set spinner position based on image dimension
						$( this ).css( {
							'margin-top': '-' + ( this.height / 2 ) + 'px',
							'margin-left': '-' + ( this.width / 2 ) + 'px'
						} );
					} );
					$this.append( $loadingSpinner );
				}
			} );
			return this;
		}

		// Else, use Spin.js defaults
		if ( !opts ) {
			opts = {};
		}
		// add color and shadow:
		opts = $.extend( { color: '#eee', shadow: true }, opts );
		this.each( function () {
			var $this = $( this ).empty(),
				thisSpinner = $this.data( 'spinner' );
			if ( thisSpinner ) {
				thisSpinner.stop();
			}
			if ( opts !== false ) {
				// eslint-disable-next-line no-new
				new Spinner( $.extend( { color: $this.css( 'color' ) }, opts ) ).spin( this );
			}
		} );
		// correct the position:
		return this;
	};

	/**
	 * Add an absolute overlay spinner useful for cases where the
	 * element does not display child elements, ( images, video )
	 *
	 * @return {jQuery}
	 */
	$.fn.getAbsoluteOverlaySpinner = function () {
		// Set the spin size to "small" ( length 5 ) if target height is small
		var spinOps = ( $( this ).height() < 36 ) ? { length: 5, width: 2, radius: 4 } : {},
			spinerSize = {
				width: 45,
				height: 45
			},
			$spinner = $( '<div />' )
				.css( {
					width: spinerSize.width,
					height: spinerSize.height,
					position: 'absolute',
					top: '50%',
					left: '50%',
					'z-index': 100
				} )
				.loadingSpinner(
					spinOps
				);
		$( this ).append( $spinner	);
		return $spinner;
	};

}( mediaWiki, jQuery ) );
