#!/bin/sh
cd /var/www/html/newcgi-bin
perl check_csf.pl > /var/www/util/logs/check_csf.log 2>&1 
