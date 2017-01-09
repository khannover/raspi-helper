#!/bin/bash

/usr/bin/python /home/pi/scripts/lcd.py -t "`date +"%d.%m.%Y %H:%M"` BOOT" -r 64 -g 16 -b 16

sleep 10

/usr/bin/python /home/pi/scripts/lcd.py -t "" -r 0 -g 0 -b 0
