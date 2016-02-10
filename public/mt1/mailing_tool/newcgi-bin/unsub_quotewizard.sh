#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_quotewizard.pl >> /var/www/util/logs/unsub_quotewizard_$a.log
