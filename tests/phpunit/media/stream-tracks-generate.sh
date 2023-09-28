# HLS streaming test tracks generation
# They are low res/bitrate for compactness; should not affect testing.
set -e

# Public domain NASA video
# see https://commons.wikimedia.org/wiki/File:Apollo_15_feather_and_hammer_drop.ogv
INFILE="https://upload.wikimedia.org/wikipedia/commons/e/e8/Apollo_15_feather_and_hammer_drop.ogv"

INTERVAL=10
MOVFLAGS="-movflags +frag_keyframe+empty_moov"
AUDFLAGS="-movflags +empty_moov -frag_duration ${INTERVAL}000000"

BITRATE="-b:v 48k"
SIZE_144="-s 176x144"
SIZE_288="-s 352x288"
SIZE_240="-s 320x240"
SIZE_360="-s 480x360"
VIDEO_VP9="-vcodec libvpx-vp9 -row-mt 1 -cpu-used 3 -g 300 $BITRATE"
VIDEO_JPEG="-vcodec mjpeg -q 999 -g 300 $BITRATE"
VIDEO_H263="-vcodec h263 -r 30000/1001 -g 300 $BITRATE"

STEREO="-ac 2 -ar 48000"
AUDIO_OPUS="-acodec libopus -ab 24k"
AUDIO_MP3="-acodec libmp3lame -ab 32k"

# Audio
ffmpeg -i "$INFILE" -vn $AUDIO_MP3 $STEREO -y stream.stereo.audio.mp3
ffmpeg -i "$INFILE" -vn $AUDIO_OPUS $STEREO $AUDFLAGS -y stream.stereo.audio.opus.mp4

# MJPEG
# because every frame is a keyframe, use the duration for fragments not keyframe boundaries!
# note .mov container is known to work on 16.3 and at least one report back to iOS 13
ffmpeg -i "$INFILE" -an $VIDEO_JPEG $AUDFLAGS $SIZE_144 -y stream.144p.video.mjpeg.mov
# .mp4 container seems to work only since iOS 16.4!
#ffmpeg -i "$INFILE" -an $VIDEO_JPEG $AUDFLAGS $SIZE_144 -y stream.144p.video.mjpeg.mp4

# VP9
ffmpeg -i "$INFILE" -an $VIDEO_VP9 $MOVFLAGS $SIZE_240 -pass 1 -y stream.240p.video.vp9.mp4
ffmpeg -i "$INFILE" -an $VIDEO_VP9 $MOVFLAGS $SIZE_240 -pass 2 -y stream.240p.video.vp9.mp4

ffmpeg -i "$INFILE" -an $VIDEO_VP9 $MOVFLAGS $SIZE_360 -pass 1 -y stream.360p.video.vp9.mp4
ffmpeg -i "$INFILE" -an $VIDEO_VP9 $MOVFLAGS $SIZE_360 -pass 2 -y stream.360p.video.vp9.mp4
