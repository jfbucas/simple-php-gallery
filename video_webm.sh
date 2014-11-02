#!/bin/bash
#
# http://stackoverflow.com/questions/3937387/rotating-videos-with-ffmpeg
#
# ffmpeg -i in.mov -vf "transpose=1" out.mov
#
# For the transpose parameter you can pass:
#
# 0 = 90CounterCLockwise and Vertical Flip (default)
# 1 = 90Clockwise
# 2 = 90CounterClockwise
# 3 = 90Clockwise and Vertical Flip
#
# https://trac.ffmpeg.org/wiki/Create%20a%20thumbnail%20image%20every%20X%20seconds%20of%20the%20video
# 

for v in Photos/*/*.mp4 Photos/*/*/*.mp4; do
	echo " + Working on $v"

	webm="${v/.mp4/.webm}"
	webm90="${v/.mp4/_090.webm}"
	webm180="${v/.mp4/_180.webm}"
	webm270="${v/.mp4/_270.webm}"
	f="$(basename "$v")"
	d="$(dirname "$v")"

	if [ ! -f "$webm" -a ! -f "$webm90" -a ! -f "$webm180" -a ! -f "$webm270" ]; then
		echo "========[[ Transcoding $v to WebM ]]"
		avconv -i "$v" -vf "scale=800:-1"                         "$webm"
		avconv -i "$v" -vf "transpose=1,scale=450:-1"             "$webm90"
		avconv -i "$v" -vf "transpose=2,scale=450:-1"             "$webm270"
		avconv -i "$v" -vf "transpose=2,transpose=2,scale=800:-1" "$webm180"
	fi

	if [ ! -f "$webm-00001.png" -a ! -f "$webm90-00001.png" -a ! -f "$webm180-00001.png" -a ! -f "$webm270-00001.png" ]; then
		for p in "$webm" "$webm90" "$webm180" "$webm270" ]; do
			ffmpeg -i "$p" -ss 00:00:01 -f image2 -vframes 1 "$p-00001.png"
		done
	fi
done


