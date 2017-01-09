#!/bin/bash

RED="-r 64 -g 32 -b 32"
BLUE="-r 32 -g 32 -b 64"
GREEN="-r 32 -g 64 -b 32"
OFF="-r 0 -g 0 -b 0"
CALENDAR="Timebox"

if [ "$1" == "" ]; then
  PRERUN="+15 minutes"
else
  PRERUN=$1
fi

if [ "$2" == "" ]; then
  SEARCH="TV"
else
  SEARCH=$2
fi

if [ "$3" == "" ]; then
  COLOR=$RED
else
  COLOR=${!3}
fi

outfile=/tmpfs/calinfo.dat
from=`date +"%m/%d/%y %H:%M"`
to=`date --date="$PRERUN" +"%m/%d/%y %H:%M"`
echo /usr/local/bin/gcalcli  --calendar $CALENDAR --military --nocolor search "$SEARCH" "$from" "$to" | grep -v "^$"
/usr/local/bin/gcalcli  --calendar $CALENDAR --military --nocolor search "$SEARCH" "$from" "$to" | grep -v "^$"  > $outfile

while read line; do
  if [ "`echo $line | grep -i "no events found"`" == "" ]; then
    echo /usr/bin/python /home/pi/scripts/lcd.py -t \"`echo ${line:19:16} | sed 's/'$SEARCH' //g'`\" $COLOR
    /usr/bin/python /home/pi/scripts/lcd.py -t "`echo ${line:19:16} | sed 's/'$SEARCH' //g'`" $COLOR
  else
    /usr/bin/python /home/pi/scripts/lcd.py -t ""  $OFF
  fi
done < $outfile

exit
