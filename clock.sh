#!/bin/bash

# Minutes
#        0   5  10  15  20  25  30  35  40  45  50  55
RED=(  115   0   0   0   0 120 250 255 255 255 238 115)
GREEN=(255 255 255 247 128   0   0   0   0 145 255 255)
BLUE=(   0   4 132 255 255 255 255 255 132   0   0   0)

let min=`date +%M`/5
echo $min
echo ${RED[$min]} ${GREEN[$min]} ${BLUE[$min]}
echo /usr/bin/python /home/pi/scripts/lcd.py -t "`date +"%d.%m.%Y %H:%M"`" -r ${RED[$min]} -g ${GREEN[$min]} -b ${BLUE[$min]}
/usr/bin/python /home/pi/scripts/lcd.py -t "`date +"%d.%m.%Y %H:%M"`" -r ${RED[$min]} -g ${GREEN[$min]} -b ${BLUE[$min]}

exit
