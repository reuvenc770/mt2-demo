#!/bin/sh
a=`date +%Y%m%d`
cd /var/www/html/newcgi-bin
#ftp -n <<EOF
#open ftp.exchange.webclients.net 1024
#user lmp_parsmedia Webclients2201 
#cd LMP_removal
#cd download
#get LMP_removal_incremental-$a.zip
#bye
#EOF
#mkdir -p proads
#/usr/local/bin/unstuff LMP_removal_incremental-$a.zip -d=proads
#rm LMP_removal_incremental-$a.zip
#perl unsub_proads.pl proads/email_incremental-$a.txt 678 > /var/www/util/logs/unsub_proads_$a.log
#rm -Rf proads
ftp -n <<EOF
open ftp.exchange.webclients.net 1024
user lmp_xlmarketing T0-3N^^nbxpqw|f 
cd LMP_removal
cd download
get LMP_removal_incremental-$a.zip
bye
EOF
mkdir -p webclients 
/usr/local/bin/unstuff LMP_removal_incremental-$a.zip -d=webclients
rm LMP_removal_incremental-$a.zip
perl unsub_proads.pl webclients/email_incremental-$a.txt 18 >> /var/www/util/logs/unsub_proads_$a.log
rm -Rf webclients 
