/**
 * Runs all the triggers on all the named bindings of an object with
 * a single callback
 *
 * NOTE THIS REQUIRES JQUERY 1.4.2 and above
 *
 * Normal jQuery tirgger calls will run the callback directly
 * multiple times for every binded function.
 *
 * With triggerQueueCallback() master callback is not called until all the
 * binded events have run their local callbacks.
 *
 * This is useful in cases where you have multiple modules that need to do asynchronous loads
 * before issuing the a callback.
 */
( function ( mw, $ ) {
	var eventArray = {};

	$.fn.bindQueueCallback = function ( eventName, callback ) {
		var targetObject = this[ 0 ];

		if ( !eventArray[ eventName ] ) {
			eventArray[ eventName ] = [];
		}

		eventArray[ eventName ].push( { targetObject: targetObject, callback: callback } );
	};

	/**
	 * @param {string} triggerName Name of trigger to be run
	 * @param {Object} triggerParam Optional arguments object to be passed to the callback
	 * @param {Function} callback Function called once all triggers have been run
	 */
	$.fn.triggerQueueCallback = function ( triggerName, triggerParam, callback ) {
		var callbackSet, callbackCount, callInx, callbackData, triggerArgs,
			targetObject = this;
		if ( !targetObject.length ) {
			mw.log( 'Error:: triggerQueueCallback: no targetObject to trigger event on' );
			return;
		}
		// Support optional triggerParam data
		if ( !callback && typeof triggerParam === 'function' ) {
			callback = triggerParam;
			triggerParam = null;
		}

		// Get the callback set
		callbackSet = eventArray[ triggerName ];

		// Check if no triggerEventSet was found:
		if ( !callbackSet || callbackSet.length === 0 ) {
			// no events return callback directly
			callback();
			return;
		}

		// Set the callbackCount
		callbackCount = ( callbackSet.length ) ? callbackSet.length : 1;
		// mw.log("mwEmbed::jQuery.triggerQueueCallback: " + triggerName
		// + ' number of queued functions:' + callbackCount );
		callInx = 0;
		callbackData = [];
		function doCallbackCheck() {
			var args = $.makeArray( arguments );
			// If only one argument don't use an array:
			if ( args.length === 1 ) {
				args = args[ 0 ];
			}
			// Add the callback data for the current trigger:
			callbackData.push( args );
			callInx++;

			// If done with loading run master callback with callbackData
			if ( callInx === callbackCount ) {
				callback( callbackData );
			}
		}
		triggerArgs = ( triggerParam ) ? [ triggerParam, doCallbackCheck ] : [ doCallbackCheck ];

		$( this ).trigger( triggerName, triggerArgs );

		$.each( eventArray[ triggerName ], function ( idx, queuedCallback ) {
			queuedCallback.callback.apply( queuedCallback.targetObject, triggerArgs );
		} );
	};
}( mediaWiki, jQuery ) );
