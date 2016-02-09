#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl unsub_antevenio.pl >> /var/www/util/logs/unsub_antevenio_$a.log
