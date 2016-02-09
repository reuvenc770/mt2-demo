#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_mdeg.pl >> /var/www/util/logs/unsub_mdeg_$a.log
