/**
 * dependencies: [ mw ]
 */
( function ( mw ) {

	/**
	 * Given a float number of seconds, returns npt format response. ( ignore
	 * days for now )
	 *
	 * @param {Float}
	 *            sec Seconds
	 * @param {Boolean}
	 *            showMs If milliseconds should be displayed.
	 * @return {Float} String npt format
	 */
	mw.seconds2npt = function ( sec, showMs ) {
		var tm, hoursStr;

		if ( isNaN( sec ) ) {
			mw.log( 'Warning: mediawiki.UtilitiesTime, trying to get npt time on NaN:' + sec );
			return '0:00:00';
		}

		tm = mw.seconds2Measurements( sec );

		// Round the number of seconds to the required number of significant
		// digits
		if ( showMs ) {
			tm.seconds = Math.round( tm.seconds * 1000 ) / 1000;
		} else {
			tm.seconds = Math.round( tm.seconds );
		}
		if ( tm.seconds < 10 ) {
			tm.seconds = '0' +	tm.seconds;
		}
		if ( tm.hours === 0 ) {
			hoursStr = '';
		} else {
			if ( tm.minutes < 10 ) { tm.minutes = '0' + tm.minutes; }

			hoursStr = tm.hours + ':';
		}
		return hoursStr + tm.minutes + ':' + tm.seconds;
	};

	/**
	 * Given seconds return object with 'days', 'hours', 'min', 'seconds'
	 *
	 * @param {number} sec Seconds to be converted into time measurements
	 * @return {Object} Parsed time
	 */
	mw.seconds2Measurements = function ( sec ) {
		var tm = {};
		tm.days = Math.floor( sec / ( 3600 * 24 ) );
		tm.hours = Math.floor( sec / 3600 );
		tm.minutes = Math.floor( ( sec / 60 ) % 60 );
		tm.seconds = sec % 60;
		return tm;
	};
	/**
	* Given a timeMeasurements object return the number of seconds
	* @param {object} timeMeasurements
	* @return {float} seconds
	*/
	mw.measurements2seconds = function ( timeMeasurements ) {
		var seconds = 0;
		if ( timeMeasurements.days ) {
			seconds += parseInt( timeMeasurements.days, 10 ) * 24 * 3600;
		}
		if ( timeMeasurements.hours ) {
			seconds += parseInt( timeMeasurements.hours, 10 ) * 3600;
		}
		if ( timeMeasurements.minutes ) {
			seconds += parseInt( timeMeasurements.minutes, 10 ) * 60;
		}
		if ( timeMeasurements.seconds ) {
			seconds += parseInt( timeMeasurements.seconds, 10 );
		}
		if ( timeMeasurements.milliseconds ) {
			seconds += parseFloat( timeMeasurements.milliseconds ) / 1000;
		}
		return seconds;
	};

	/**
	 * Take hh:mm:ss,ms or hh:mm:ss.ms input, return the number of seconds
	 *
	 * @param {string} nptString NPT time string
	 * @return {number} Number of seconds
	 */
	mw.npt2seconds = function ( nptString ) {
		var hour, min, sec, times;

		if ( !nptString ) {
			// mw.log( 'npt2seconds: not valid ntp: ' + ntp );
			return 0;
		}

		// Strip {npt:}01:02:20 or 32{s} from time if present
		nptString = nptString.replace( /npt:|s/g, '' );

		hour = 0;
		min = 0;

		// Sometimes a comma is used instead of period for ms
		times = nptString.replace( /,\s?/, '.' ).split( ':' );
		sec = +( times.pop() || 0 );
		min = +( times.pop() || 0 );
		hour = +( times.pop() || 0 );

		// Return seconds
		return ( hour * 3600 ) + ( min * 60 ) + sec;
	};

}( mediaWiki ) );
