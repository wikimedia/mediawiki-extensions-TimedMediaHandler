#!/bin/sh
# Script used to encode ffmpeg.
export TMH_FFMPEG_PATH="${TMH_FFMPEG_PATH:-ffmpeg}"
export TMH_FFMPEG_PASSES="${TMH_FFMPEG_PASSES:-1}"
export TMH_FFMPEG_THREADS="${TMH_FFMPEG_THREADS:-2}"
# Safe options with no user input or validated user input
# These can be used unquoted.
# Video related
export TMH_FFMPEG2_OPTS TMH_MOVFLAGS TMH_OPTS_VIDEO TMH_REMUX TMH_OPT_SPEED
# Audio-related
export TMH_OPTS_AUDIO TMH_OPT_NOAUDIO




doFfmpegEncode() {
	current_pass="${1:-0}"
	if [ ! -x "$TMH_FFMPEG_PATH" ]; then
		echo "error: executable '$TMH_FFMPEG_PATH not found";
		exit 1
	fi


	# Non-copy video opts that depend on the pass
	if [ "$TMH_OPTS_VIDEO" = "-vn" ] && [ "$TMH_REMUX" = "no" ]; then
		case "$TMH_OPT_VIDEOCODEC" in
			vp8|vp9)
				if [ "$current_pass" -eq 1 ]; then
					# Make first pass faster...
					TMH_OPTS_VIDEO="${TMH_OPTS_VIDEO} -speed 4"
				elif [ "$TMH_OPT_SPEED" != "" ]; then
					TMH_OPTS_VIDEO="$TMH_OPTS_VIDEO -speed $TMH_OPT_SPEED"
				fi
			;;
			*)
			;;
		esac
	fi

	## Set up audio opts if noaudio is selected and we're on pass number 1
	if [ "$current_pass" -eq 1 ] && [ "$TMH_OPT_NOAUDIO" = "yes" ]; then
		TMH_OPTS_AUDIO="-an"
	fi

	# end audio options
	PASS_OPTS=""
	OUTPUT="transcoded.video"
	if [ "$current_pass" -ne 0 ]; then
		PASS_OPTS="-pass $current_pass -passlogfile ${OUTPUT}.log"
	fi
	# Do not output a file on first pass of a multi-pass encoding
	if [ "$current_pass" -eq 1 ]; then
		OUTPUT="/dev/null"
	fi


	# Please note, the unquoted entries are unquoted on purpose here.
	"$TMH_FFMPEG_PATH" -nostdin -y -i original.video \
		$TMH_OPTS_VIDEO \
		$FFMPEG2_OPTS \
		$TMH_OPTS_AUDIO \
		$PASS_OPTS \
		$TMH_MOVFLAGS \
		$OUTPUT
}

if [ "$TMH_PASSES" -lt 2 ]; then
	doFfmpegEncode
else
	# two passes encoding
	doFfmpegEncode 1
	doFfmpegEncode 2
fi
