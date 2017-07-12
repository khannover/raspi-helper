#!/bin/bash

TMP=/tmpfs/inet.stat
CURL=/usr/bin/curl
TOLERANCE=10 # minutes without network

INETSTATUS=`$CURL -Is http://www.google.de | grep HTTP | cut -d " " -f 2`
ROUTERSTATUS=`$CURL -Is http://fritz.box | grep HTTP | cut -d " " -f 2`
if [ $? -eq 0 -a $INETSTATUS -eq 200 ]; then
  if [ -f $TMP ]; then
    rm $TMP
  fi
  exit 0
else
  if [ -f $TMP ]; then
    lastdate=`cat $TMP`
    now=`date +%s`
    diff=0
    let diff="($now - $lastdate)/60"
    if [ $diff -gt $TOLERANCE ]; then
      # let's try to reconnect before reboot
      echo "No network connection, restarting wlan0"
      sudo ifdown 'wlan0'
      sleep 5
      sudo ifup --force 'wlan0'
      INETSTATUS=`$CURL -Is http://www.google.de | grep HTTP | cut -d " " -f 2`
      if [ $? -eq 0 -a $INETSTATUS -eq 200 ]; then
          # we're lucky. the network is back :-)
          rm $TMP
      else
          # seems that nothing helped so far... let's reboot now
          sudo reboot
      fi
    fi
  else
    echo `date +%s` > $TMP
  fi
fi

exit
