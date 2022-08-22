'use strict';

// Build a table of target output bitrates to be used
// for reference editing WebVideoTranscode.php

let heights = [
    120,
    180,
    240,
    360,
    480,
    720,
    1080,
    1440,
    2160
];

let base = 480;
let exponent = 0.85;

function area(h) {
    let w = Math.round(h * 16 / 9);
    return w * h;
}

function rate(h, bitrate) {
    let ratio = area(h) / area(base);
    return Math.round(bitrate * (ratio ** exponent));
}

let codecs = [
    ['vp9', 1000],
    ['vp8', 1280],
    ['h264', 1280]
];

for (let [codec, bitrate] of codecs) {
    console.log(codec);
    for (let h of heights) {
        console.log(`${h}: ${rate(h, bitrate)}`);
    }
    console.log('');
}