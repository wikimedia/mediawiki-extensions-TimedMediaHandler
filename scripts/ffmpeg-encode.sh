#!/bin/sh
set -x
# Script used to encode ffmpeg.
export TMH_FFMPEG_PATH="${TMH_FFMPEG_PATH:-ffmpeg}"
export TMH_FFMPEG_PASSES="${TMH_FFMPEG_PASSES:-1}"
export TMH_FFMPEG_THREADS="${TMH_FFMPEG_THREADS:-2}"
# Safe options with no user input or validated user input
# These can be used unquoted.
export TMH_OPTS_SOURCE
# Video related
export TMH_MOVFLAGS TMH_OPTS_VIDEO TMH_REMUX TMH_OPT_SPEED TMH_OPT_VIDEOCODEC
# Audio-related
export TMH_OPTS_AUDIO TMH_OPT_NOAUDIO
export TMH_OUTPUT_FILE
# Options containing spaces
export TMH_ADAPTATION_SETS


doFfmpegEncode() {
	current_pass="${1:-0}"
	if [ ! -x "$(command -v $TMH_FFMPEG_PATH)" ]; then
		echo "error: executable '$TMH_FFMPEG_PATH not found";
		exit 1
	fi


	# Non-copy video opts that depend on the pass
	PASS_OPTS=
	if [ "$current_pass" -ne 0 ]; then
		PASS_OPTS="-pass $current_pass"
	fi
	if [ "$TMH_REMUX" = "no" ]; then
		case "$TMH_OPT_VIDEOCODEC" in
			vp9)
				if [ "$current_pass" -eq 1 ]; then
					# Make first pass faster...
					PASS_OPTS="$PASS_OPTS -speed 4"
				elif [ "$TMH_OPT_SPEED" != "" ]; then
					PASS_OPTS="$PASS_OPTS -speed $TMH_OPT_SPEED"
				fi
			;;
			*)
			;;
		esac
	fi

	## Set up audio opts if noaudio is selected or we're on pass number 1
	if [ "$current_pass" -eq 1 ] || [ "$TMH_OPT_NOAUDIO" = "yes" ]; then
		AUDIO_OPTS="-an"
	else
		AUDIO_OPTS="$TMH_OPTS_AUDIO"
	fi
	# end audio options

	# Assemble options
	# Please note, the unquoted entries are unquoted on purpose here.
	set -- -nostdin -nostats -y \
		$TMH_OPTS_SOURCE \
		$TMH_OPTS_VIDEO \
		$AUDIO_OPTS \
		$PASS_OPTS \
		$TMH_MOVFLAGS

	# Add adaptation sets (a space-delimited string)
	if [ -n "$TMH_ADAPTATION_SETS" ]; then
		set -- "$@" -adaptation_sets "$TMH_ADAPTATION_SETS"
	fi

	# Do not output a file on first pass of a multi-pass encoding
	if [ "$current_pass" -eq 1 ]; then
		set -- "$@" /dev/null
	else
		set -- "$@" "$TMH_OUTPUT_FILE"
	fi

	"$TMH_FFMPEG_PATH" "$@"
}

if [ "$TMH_FFMPEG_PASSES" -lt 2 ]; then
	doFfmpegEncode
else
	# two passes encoding
	if ! doFfmpegEncode 1; then
		echo "error running first-pass of ffmpeg encoding"
		exit 1
	fi
	doFfmpegEncode 2
fi
