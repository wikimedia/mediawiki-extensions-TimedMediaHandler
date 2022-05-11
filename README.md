# TimedMediaHandler

This extension provides a media handler for the Ogg, WebM, mp4 container format.
When enabled, a player will be automatically embedded in the file description
page, or any wiki page while still using the same syntax as for images.

* Broad support for input file formats
* Transcoder to make video at web resolutions when embedding clips in a page
* Include support for Timed Text per the w3c "track" recommendation
* use Video.js javascript player for playback

After you installed this extension, add the following to the end of your
`LocalSettings.php` to enable it:

```
  // TimedMediaHandler
  wfLoadExtension( 'TimedMediaHandler' );
```

Configuration documentation is canonically provided at:
https://www.mediawiki.org/wiki/Extension:TimedMediaHandler

## Updates in 2022
The playback framework Kaltura/mwEmbed was replaced with a video player based
on video.js

## Updates in 2018

Ogg Theora (.ogv) video output has been removed due to ongoing issues with
ffmpeg2theora and libtheora packaging. WebM is now be used as the preferred
royalty-free video output by default. Ogg files are still supported, but
videos will be transcoded to WebM.

If your `LocalSettings.php` used one of the `WebVideoTranscode::ENC_OGV_160P` etc
constants, you may need to remove them after updating to a current version.

The `$wgEnabledTranscodeSet` and `$wgEnabledAudioTranscodeSet` config variables
have changed! If you have manually configured them in `LocalSetings.php`, you
MUST update them:

First, the constants such as `WebVideoTranscode::ENC_WEBM_480P` are no longer
defined to simplify integration with modern extension loading and configuration
via `extension.json`. Instead, use the string values such as `'480p.webm'`.

Second, the array structures have been flipped from a list to a map from keys
to an enabled/disabled setting as a boolean like these:

```
  $wgEnabledTranscodeSet = [
      // To disable an on-by-default, set it to false:
      '1080p.webm' => false,
      // To enable an off-by-default, set it to true:
      '1440p.vp9.webm' => true,
  ];
```
or item-by-item:

```
  // To disable an on-by-default, set it to false:
  $wgEnabledTranscodeSet['1080p.webm'] = false;
  // To enable an off-by-default, set it to true:
  $wgEnabledTranscodeSet['1440p.vp9.webm'] = true;
```

Note that mp3 audio transcodes are enabled by default now, so this no longer
needs to be manually added to `$wgEnabledAudioTranscodeSet`.

## Running Transcodes

Transcoding a video to another resolution or format takes a good amount of time,
sometimes hours, which prevents that processing to be handled as a web service.
Instead, the extension implements an asynchronous job, named webVideoTranscode,
which you must be running regularly as your web server user.

The job can be run using the MediaWiki `maintenance/runJobs.php` utility (do not
forget to su as a webserver user):

```
  php runJobs.php --type webVideoTranscode --maxjobs 1
  php runJobs.php --type webVideoTranscodePrioritized --maxjobs 1
```

## Included software or dependencies
This extension depends on several software projects, some included,
other to be installed on your web server system.

### Video.js HTML5 player library
TimedMediaHandler uses the Video.js HTML5 web media player.
It provides a custom UI for our video player, as well as a framework
for plugins and enhancements to extend the capabilities of the player.

For more information about the player library visit:
https://videojs.com

Video.js code is released under the Apache 2.0 License:
http://www.apache.org/licenses/LICENSE-2.0

### Ogv.js decoder
Brion Vibber, a Wikimedia developer, created the JavaScript
compatibility shim Ogv.js. It is a software decoding
library for the file formats Ogg and WebM and the Vorbis,
Theora, VP8 and VP9 codecs. It allows web browser without native
HTML5 video support like iOS to support these formats.

For more information about ogv.js visit:
https://github.com/brion/ogv.js/

Ogv.js code is released under the MIT license:
https://opensource.org/licenses/MIT

### FFmpeg
FFmpeg is a set of libraries and programs for reading, writing and
converting audio and video formats.

We use ffmpeg for two purposes:
 - creating still images of videos (aka thumbnails)
 - transcoding between WebM, Ogg and/or H.264 videos

Wikimedia currently uses ffmpeg as shipped in Debian 10.
For best experience use that or any later release from https://ffmpeg.org

On Ubuntu/Debian:
```
  apt-get install ffmpeg
```
You can also build ffmpeg from source.
Guide for building ffmpeg with H.264 for Ubuntu:
https://ffmpeg.org/trac/ffmpeg/wiki/UbuntuCompilationGuide

Some old versions of FFmpeg had a bug which made it extremely slow to seek in
large theora videos in order to generate a thumbnail. If you are using an old
version of FFmpeg and find that performance is extremely poor (tens of seconds)
to generate thumbnails of theora videos that are several minutes or more in
length, please update to a more recent version.

In MediaWiki configuration, after the require line in LocalSettings.php, you
will have to specify the FFmpeg binary location with:

```
    $wgFFmpegLocation = '/path/to/ffmpeg';
```

Default being `/usr/bin/ffmpeg`.

For more information about FFmpeg visit:
https://ffmpeg.org

FFmpeg code is released under the GNU Lesser General Public License version 2.1
4 or later (LGPL v2.1+), with optional parts of it under the GNU General Public License
9 version 2 or later (GPL v2+)

### PEAR File_Ogg

Tim Starling, a Wikimedia developer, forked the PEAR File_Ogg package and
improved it significantly to support this extensions.

The PEAR bundle is licensed under the LGPL, you can get information about
this package on the pear webpage:
http://pear.php.net/package/File_Ogg

### getID3

getID3 is used for metadata of WebM files. It is marked as a dependency
to be installed via composer.

getID3() by James Heinrich <info@getid3.org>
available at http://getid3.sourceforge.net
or http://www.getid3.org/

getID3 code is released under the GNU GPL:
http://www.gnu.org/copyleft/gpl.html
