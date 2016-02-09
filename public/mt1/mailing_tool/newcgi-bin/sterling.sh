#!/bin/sh
a=`date +%m%d%Y`
cd /var/www/html/newcgi-bin
perl sterling.pl > /var/www/util/logs/sterling_$a.log
