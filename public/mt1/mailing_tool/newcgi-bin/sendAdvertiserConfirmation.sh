#!/bin/sh
a=`date +%Y%m%d`
cd /var/www/html/newcgi-bin
perl sendAdvertiserConfirmation.pl  > /var/www/util/logs/sendAdvertiserConfirmation_$a.log
