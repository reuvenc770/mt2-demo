#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_orangevyped.pl >> /var/www/util/logs/unsub_orangevyped_$a.log
