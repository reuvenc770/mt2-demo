#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_reich.pl >> /var/www/util/logs/unsub_reich_$a.log
