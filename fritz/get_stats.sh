#!/bin/bash

DATADIR=/home/pi/inet-usage-data
FRITZPY=/home/pi/scripts/fritz/fritz.py

if [ ! -d $DATADIR ]; then
  mkdir -p $DATADIR
fi

sleep 20
if [ "`pgrep fritz.py`" != "" ]; then
    echo "fritz.py is already running"
    exit 1
fi
/usr/bin/python $FRITZPY  | grep 'class="time"' | awk -F">" '{print $2";"$4";"$6";"$8";"}' | sed 's/<\/td//g' > /tmp/fbstats.txt

count=0
while read line; do
  let count="$count+1"
  case $count in
    1)
       day=`date +%d.%m.%Y` ;
       time=`echo $line | awk -F";" '{print $2}'`
       if [ "`grep $time $DATADIR/$day.dat`" == "" ]; then
         echo "$day;$line" >> $DATADIR/$day.dat ;
       fi
       ;;
    2)
       day=`date --date=yesterday +%d.%m.%y` ;
       ;;
    3)
       day=`date +%Y/%V` ;;
    4)
       day=`date +%m/%Y` ;;
    5)
       day=`date --date="1 month ago" +%m/%Y` ;;
  esac
done < /tmp/fbstats.txt

tail -n 1  -q /home/pi/inet-usage-data/* > /home/pi/inet-usage-data/total.dat
