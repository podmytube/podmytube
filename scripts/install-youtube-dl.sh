#!/bin/bash
# This script is installing youtube-dl
# Youtube-dl is installed on host machine and mounted on container
# I did not find another way to update it simply
# python should be installed on container

#
# Installing youtube-dl
#
sudo curl -L https://yt-dl.org/downloads/latest/youtube-dl -o /usr/local/bin/youtube-dl

sudo chmod a+rx /usr/local/bin/youtube-dl