/**
 * mwEmbed jQuery utility functions that are too small for their own file
 */
( function ( $ ) {
	var mwDefaultButtonOptions;

	/**
	 * Extend Unique to work with strings and number values
	 * http://paulirish.com/2010/duck-punching-with-jquery/
	 *
	 * @param {Array} arr
	 * @return {Array}
	 */
	$.uniqueArray = function ( arr ) {
		// Do the default behavior only if we got an array of elements
		if ( arr.length === 0 || !!arr[ 0 ].nodeType ) {
			return $.unique.apply( this, arguments );
		} else {
			// reduce the array to contain no dupes via grep/inArray
			return $.grep( arr, function ( v, k ) {
				return $.inArray( v, arr ) === k;
			} );
		}
	};

	/**
	 * Shortcut to a themed button Should be depreciated for $.button
	 * bellow
	 *
	 * @param {string} msg
	 * @param {string} styleClass
	 * @param {string} iconId
	 * @param {Object} [opt]
	 * @return {string}
	 */
	$.btnHtml = function ( msg, styleClass, iconId, opt ) {
		var href, targetAttr, styleAttr;
		opt = opt || {};
		href = opt.href || '#';
		targetAttr = opt.target ? ' target="' + opt.target + '" ' : '';
		styleAttr = opt.style ? ' style="' + opt.style + '" ' : '';
		return '<a href="' + href + '" ' + targetAttr + styleAttr +
			' class="ui-state-default ui-corner-all ui-icon_link ' +
			styleClass + '"><span class="ui-icon ui-icon-' + iconId + '" ></span>' +
			'<span class="btnText">' + msg + '</span></a>';
	};

	// Shortcut to generate a jQuery button
	mwDefaultButtonOptions = {
		// The class name for the button link
		'class': '',

		// The style properties for the button link
		style: { },

		// The text of the button link
		text: '',

		// The icon id that precedes the button link:
		icon: 'carat-1-n'
	};

	$.button = function ( options ) {
		var $button;

		options = $.extend( {}, mwDefaultButtonOptions, options );

		// Button:
		$button = $( '<a>' )
			.attr( 'href', '#' )
			.addClass( 'ui-state-default ui-corner-all ui-icon_link' );
		// Add css if set:
		if ( options.css ) {
			$button.css( options.css );
		}

		if ( options.class ) {
			$button.addClass( options.class );
		}

		// return the button:
		$button.append(
			$( '<span />' ).addClass( 'ui-icon ui-icon-' + options.icon ),
			$( '<span />' ).addClass( 'btnText' )
				.text( options.text )
		)
			.buttonHover(); // add buttonHover binding;
		if ( !options.text ) {
			$button.css( 'padding', '1em' );
		}
		return $button;
	};

	// Shortcut to bind hover state
	$.fn.buttonHover = function () {
		$( this ).hover(
			function () {
				$( this ).addClass( 'ui-state-hover' );
			},
			function () {
				$( this ).removeClass( 'ui-state-hover' );
			}
		);
		return this;
	};
}( jQuery ) );
