#!/bin/sh
a=`date +%m%d%Y`
cd /var/www/html/newcgi-bin
perl sms_test.pl > /var/www/util/logs/sms_test_$a.log
