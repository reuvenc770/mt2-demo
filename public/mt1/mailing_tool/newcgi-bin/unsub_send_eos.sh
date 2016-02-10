#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_send_eos.pl >> /var/www/util/logs/unsub_send_eos_$a.log
