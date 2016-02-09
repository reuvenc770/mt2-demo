#!/bin/sh
a=`date +%m%d%Y`
cd /var/www/html/newcgi-bin
ftp -n ftp.aspiremail.com<<EOF
user ward2 jd8@e#_xd!
ascii
get unsubs_educationbridge.txt 
rename unsubs_educationbridge.txt processed/unsubs_educationbridge_$a.txt
bye
EOF
perl unsub_ward.pl unsubs_educationbridge.txt >> /var/www/util/logs/unsub_ward_$a.log
