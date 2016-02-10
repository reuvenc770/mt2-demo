#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_planet49es.pl >> /var/www/util/logs/unsub_planet49es_$a.log
