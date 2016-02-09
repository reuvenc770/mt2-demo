#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_get_eos.pl >> /var/www/util/logs/unsub_get_eos_$a.log
