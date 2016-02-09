#!/bin/sh
a=`date +%Y%m%d`
cd /var/www/html/newcgi-bin
perl copy_campaigns.pl > /var/www/util/logs/copy_campaigns_$a.log
