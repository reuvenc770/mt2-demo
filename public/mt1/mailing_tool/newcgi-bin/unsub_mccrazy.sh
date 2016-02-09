#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_mccrazy.pl >> /var/www/util/logs/unsub_mccrazy_$a.log
