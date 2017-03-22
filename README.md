# raspi-helper
Helper scripts for Raspberry Pi / GrovePi+

* rebootOnNetworkLoss.sh
  * Reboots the raspberry pi after a configurable amount of minutes without internet connection due to low power supply
* lcd.py
  * Takes 4 Parameters:  lcd.py -t \<text\> -r \<int\> -g \<int\> -b \<int\>
  * Breakes the text input in segments that to the character size of the diplay (16x2)
* getEvents.sh
  * uses gcalcli
  * gets events from google calendar and displays it via lcd.py
* clock.sh
  * shows the current time with a background color that depends on the minutes
  * uses lcd.py
* bootup.sh
  * shows a message on boot via lcd.py
