#!/bin/sh
a=`date +%m%d%Y`
/var/www/html/newcgi-bin/ftp.pl >> /var/www/util/logs/push_$a.log 2>&1
