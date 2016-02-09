#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_virtumundo.pl >> /var/www/util/logs/unsub_virtumundo_$a.log
