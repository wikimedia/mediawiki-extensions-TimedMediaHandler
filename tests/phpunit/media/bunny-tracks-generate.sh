# Audio test tracks generation
# They are short for compactness; should not affect testing.
set -e

# Blender open source movie soundtrack
# This comes in 5.1-channel surround sound, Vorbis source track
# see https://commons.wikimedia.org/wiki/File:Big_Buck_Bunny_4K.webm
INFILE="https://upload.wikimedia.org/wikipedia/commons/c/c0/Big_Buck_Bunny_4K.webm"

# Note there's a FLAC-only soundtrack file, but ffmpeg treats the channel
# layout differently for FLAC than for Opus and Vorbis and it's funky
# for some reason. Using the WebM/Vorbis source as a more realistic example.

INTERVAL=10
FRAG="-movflags +empty_moov -frag_duration ${INTERVAL}000000"

AUDIO_OPUS_LO="-acodec libopus -ab 48k"
AUDIO_OPUS_HI="-acodec libopus -ab 96k"
AUDIO_MP3="-acodec libmp3lame -ab 64k"
AUDIO_VORBIS_LO="-acodec libvorbis -ab 64k"
AUDIO_VORBIS_HI="-acodec libvorbis -ab 128k"
AUDIO_WAV="-acodec pcm_s16le"
AUDIO_FLAC="-acodec flac"

SKIPTO=14
SECONDS=1
CLIP="-ss $SKIPTO -t $SECONDS"

COMMON="-vn -ar 48000"
STEREO="$COMMON -ac 2"
SURROUND="$COMMON"

ffmpeg -i "$INFILE" $CLIP $STEREO   $AUDIO_MP3            -y bunny.stereo.audio.mp3

ffmpeg -i "$INFILE" $CLIP $STEREO   $AUDIO_OPUS_LO        -y bunny.stereo.audio.opus
ffmpeg -i "$INFILE" $CLIP $STEREO   $AUDIO_OPUS_LO        -y bunny.stereo.audio.opus.webm
ffmpeg -i "$INFILE" $CLIP $STEREO   $AUDIO_OPUS_LO $FRAG  -y bunny.stereo.audio.opus.mp4
ffmpeg -i "$INFILE" $CLIP $SURROUND $AUDIO_OPUS_HI        -y bunny.surround.audio.opus
ffmpeg -i "$INFILE" $CLIP $SURROUND $AUDIO_OPUS_HI        -y bunny.surround.audio.opus.webm
ffmpeg -i "$INFILE" $CLIP $SURROUND $AUDIO_OPUS_HI $FRAG  -y bunny.surround.audio.opus.mp4

ffmpeg -i "$INFILE" $CLIP $STEREO   $AUDIO_VORBIS_LO      -y bunny.stereo.audio.vorbis.ogg
ffmpeg -i "$INFILE" $CLIP $STEREO   $AUDIO_VORBIS_LO      -y bunny.stereo.audio.vorbis.webm
ffmpeg -i "$INFILE" $CLIP $SURROUND $AUDIO_VORBIS_HI      -y bunny.surround.audio.vorbis.ogg
ffmpeg -i "$INFILE" $CLIP $SURROUND $AUDIO_VORBIS_HI      -y bunny.surround.audio.vorbis.webm

ffmpeg -i "$INFILE" $CLIP $STEREO   $AUDIO_WAV            -y bunny.stereo.audio.pcm16.wav
ffmpeg -i "$INFILE" $CLIP $SURROUND $AUDIO_WAV            -y bunny.surround.audio.pcm16.wav

ffmpeg -i "$INFILE" $CLIP $STEREO   $AUDIO_FLAC           -y bunny.stereo.audio.flac
ffmpeg -i "$INFILE" $CLIP $SURROUND $AUDIO_FLAC           -y bunny.surround.audio.flac
