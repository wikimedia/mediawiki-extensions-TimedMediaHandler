#!/bin/sh
set -x
export TMH_FLUIDSYNTH_PATH="${TMH_FLUIDSYNTH_PATH:-fluidsynth}"
export TMH_SOUNDFONT_PATH="${TMH_SOUNDFONT_PATH:-/usr/share/sounds/sf2/FluidR3_GM.sf2}"
export TMH_AUDIO_CODEC="${TMH_AUDIO_CODEC:-vorbis}"
export TMH_FFMPEG_PATH="${TMH_FFMPEG_PATH:-ffmpeg}"
export TMH_OPT_QUALITY="${TMH_OPT_QUALITY:-}"
export TMH_OPT_BITRATE="${TMH_OPT_BITRATE:-}"
export TMH_OPT_SAMPLERATE="${TMH_OPT_SAMPLERATE:-}"
export TMH_OPT_CHANNELS="${TMH_OPT_CHANNELS:-}"
export TMH_OUTPUT_FILE

if [ "$TMH_AUDIO_CODEC" = 'vorbis' ]; then
	codec='oga'
	fluidsynth_output='output_audio'
else
	codec='wav'
	fluidsynth_output='output.wav'
fi


cleanUp() {
	rm -f "$fluidsynth_output" fluidsynth.log
}

trap cleanUp EXIT

runFluidSynth() {
	codec="$1"
	# Check we can execute fluidsynth
	if [ ! -x "$(command -v "$TMH_FLUIDSYNTH_PATH")" ]; then
		echo "error: executable '$TMH_FLUIDSYNTH_PATH not found";
		exit 1
	fi
	# check the soundfont is present and readable
	if [ ! -r "$TMH_SOUNDFONT_PATH" ]; then
		echo "error: cannot access sound font '$TMH_SOUNDFONT_PATH'";
		exit 1
	fi
	"$TMH_FLUIDSYNTH_PATH" -T "$codec" -F "$fluidsynth_output" "$TMH_SOUNDFONT_PATH" input.mid
}


runLame() {
	if [ ! -x "$TMH_FFMPEG_PATH" ]; then
		echo "error: executable '$TMH_FFMPEG_PATH not found";
		exit 1
	fi
	# clean the arglist, then add optional args to it.
	# This is the only safe way to pass variable arguments in
	# posix shell AFAICT.
	set --
	if [ "$TMH_OPT_QUALITY" != "" ]; then
		set -- -aq "$TMH_OPT_QUALITY"
	fi
	if [ "$TMH_OPT_BITRATE" != "" ]; then
		set -- "$@" -ab "$TMH_OPT_BITRATE"
	fi
	if [ "$TMH_OPT_SAMPLERATE" != "" ]; then
		set -- "$@" -ar "$TMH_OPT_SAMPLERATE"
	fi
	if [ "$TMH_OPT_CHANNELS" != "" ]; then
		set -- "$@" -ac "$TMH_OPT_CHANNELS"
	fi
	set -- "$@" -acodec libmp3lame "$TMH_OUTPUT_FILE"
	# We willingly let TMH_LAME_ARGS expand as it has been escaped via Shell::escape
	"$TMH_FFMPEG_PATH" -y -i "$fluidsynth_output" "$@"
}


# Fluidsynth returns a 0 exit code even upon error, so we need to check the output.
runFluidSynth "$codec" > fluidsynth.log 2 >&1
if grep -q "fluidsynth: error" fluidsynth.log; then
	# Make the output available to the caller
	cat fluidsynth.log
	exit 1
fi
if [ "$TMH_AUDIO_CODEC" != 'vorbis' ]; then
	runLame
else
	mv "$fluidsynth_output" "$TMH_OUTPUT_FILE"
fi
