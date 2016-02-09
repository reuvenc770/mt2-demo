#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_planet49fr.pl >> /var/www/util/logs/unsub_planet49fr_$a.log
