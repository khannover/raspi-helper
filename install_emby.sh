#!/bin/bash

releases=https://api.github.com/repos/MediaBrowser/Emby.Releases/releases
curl=/usr/bin/curl
jq=/usr/bin/jq
debdir=/home/pi/scripts/emby-releases

dlfile=$($curl -s $releases | $jq . | grep armhf | grep download | head -1 | awk '{print $2}' | tr -d '"')

mkdir $debdir
if [ ! -f $debdir/$(basename $dlfile) ]; then
    wget $dlfile -O $debdir/$(basename $dlfile)
    sudo dpkg -i $debdir/$(basename $dlfile)
fi
