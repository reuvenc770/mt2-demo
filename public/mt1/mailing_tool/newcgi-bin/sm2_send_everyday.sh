#!/bin/sh
a=`date +%m%d%Y`
cd /var/www/html/newcgi-bin
perl sm2_send_everyday.pl > /var/www/util/logs/sm2_send_everyday_$a.log
