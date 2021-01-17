#!/bin/bash
# This script is installing youtube-dl
# Youtube-dl is installed on host machine and mounted on container
# I did not find another way to update it simply
# python should be installed on container

#
# Installing youtube-dl
#
SCRIPT_DIR=$(dirname ${0})
echo $SCRIPT_DIR
DESTINATION_DIR="$SCRIPT_DIR/../bin"
echo $DESTINATION_DIR
if [ ! -d $DESTINATION_DIR ]; then
    mkdir $DESTINATION_DIR
else
    echo "$DESTINATION_DIR exists already"
fi

curl -L https://yt-dl.org/downloads/latest/youtube-dl -o $DESTINATION_DIR/youtube-dl
chmod +rx $DESTINATION_DIR/youtube-dl
