'use strict';

// Command-line helper script for coding maintenance on TimedMediaHandler.
//
// Builds a table of target output bitrates to be used
// for reference editing WebVideoTranscode.php
//
// @todo generate the table directly from this script instead of
//       manually editing the table in WebVideoTranscode.php :)

const heights = [
	120,
	160,
	180,
	240,
	360,
	480,
	720,
	1080,
	1440,
	2160
];

const base = 480;
const exponent = 0.85;

function area( h ) {
	const w = Math.round( h * 16 / 9 );
	return w * h;
}

function rate( h, bitrate ) {
	const ratio = area( h ) / area( base );
	return Math.round( bitrate * ( ratio ** exponent ) );
}

const codecs = [
	[ 'vp9', 1000 ],
	[ 'vp8', 1250 ],
	// [ 'h264', 1000 ],
	[ 'mjpeg', 10570 ] // to get 1000 @ 120p
];

for ( const [ codec, bitrate ] of codecs ) {
	console.log( codec );
	for ( const h of heights ) {
		console.log( `${h}: ${rate( h, bitrate )} ${rate( h, bitrate * 0.5 )} ${rate( h, bitrate * 1.45 )}` );
	}
	console.log( '' );
}
