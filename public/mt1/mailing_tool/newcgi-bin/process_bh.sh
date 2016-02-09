#!/bin/sh
a=`date +%m_%d`
cd /var/www/html/newcgi-bin
perl process_bh.pl BH001 >> /var/www/util/logs/process_bh_$a.log
perl process_bh.pl BH002 >> /var/www/util/logs/process_bh_$a.log
perl process_bh.pl BH003 >> /var/www/util/logs/process_bh_$a.log
perl process_bh.pl BH004 >> /var/www/util/logs/process_bh_$a.log
perl process_bh.pl BH006 >> /var/www/util/logs/process_bh_$a.log
perl process_bh.pl BH007 >> /var/www/util/logs/process_bh_$a.log
perl process_bh.pl BH008 >> /var/www/util/logs/process_bh_$a.log
