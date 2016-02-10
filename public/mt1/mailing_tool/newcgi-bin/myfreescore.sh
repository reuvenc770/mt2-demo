#!/bin/sh
a=`date +%m%d%Y`
cd /tmp
perl /var/www/html/newcgi-bin/myfreescore.pl >> /var/www/util/logs/myfreescore_$a.log 2>&1
