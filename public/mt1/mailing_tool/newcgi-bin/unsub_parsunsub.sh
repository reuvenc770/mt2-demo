#!/bin/sh
a=`date +%Y%m%d`
cd /var/www/html/newcgi-bin
ftp -n ftp.aspiremail.com <<EOF
user parsunsub 89TF7G6g 
get LMP_removal_incremental-$a.zip
bye
EOF
mkdir -p parsunsub 
/usr/local/bin/unstuff LMP_removal_incremental-$a.zip -d=parsunsub
rm LMP_removal_incremental-$a.zip
perl unsub_proads.pl parsunsub/email_incremental-$a.txt 678 > /var/www/util/logs/unsub_parsunsub_$a.log
rm -Rf parsunsub 
