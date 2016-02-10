#!/bin/sh
a=`date +%m%d%Y`
cd /var/www/html/newcgi-bin
ftp -n ftp.aspiremail.com<<EOF
user oneononesup O90s2JKs 
ascii
get unsub_$a.txt 
bye
EOF
perl unsub_oneonone.pl unsub_$a.txt > /var/www/util/logs/unsub_oneonone_$a.log
