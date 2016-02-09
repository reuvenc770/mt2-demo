#!/bin/sh
a=`date +%m%d%Y`
/var/www/html/newcgi-bin/ftp_daily.pl >> /var/www/util/logs/push_daily_$a.log 2>&1
