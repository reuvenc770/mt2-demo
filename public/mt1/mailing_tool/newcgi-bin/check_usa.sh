#!/bin/sh
cd /var/www/html/newcgi-bin
perl check_usa.pl > /var/www/util/logs/check_usa.log 2>&1 
