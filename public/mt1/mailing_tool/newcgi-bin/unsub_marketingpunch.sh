#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_marketingpunch.pl >> /var/www/util/logs/unsub_marketingpunch_$a.log
