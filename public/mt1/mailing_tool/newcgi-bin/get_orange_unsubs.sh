#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl get_orange_unsubs.pl >> /var/www/util/logs/get_orange_unsubs_$a.log
