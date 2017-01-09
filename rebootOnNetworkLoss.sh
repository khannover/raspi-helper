#!/bin/bash

TMP=/tmpfs/inet.stat
CURL=/usr/bin/curl
TOLERANCE=10 # minutes

INETSTATUS=`$CURL -Is http://www.google.de | grep HTTP | cut -d " " -f 2`
ROUTERSTATUS=`$CURL -Is http://fritz.box | grep HTTP | cut -d " " -f 2`
if [ $? -eq 0 -a $INETSTATUS -eq 200 ]; then
  if [ -f $TMP ]; then
    rm $TMP
  fi
  exit 0
else
  lastdate=`cat $TMP`
  now=`date +%s`
  diff=0
  let diff="($now - $lastdate)/60"
  if [ $diff -gt $TOLERANCE ]; then
    sudo reboot
  else
    echo `date +%s` > $TMP
  fi
fi
