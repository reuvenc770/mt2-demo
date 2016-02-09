#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_flatiron.pl >> /var/www/util/logs/unsub_flatiron_$a.log
