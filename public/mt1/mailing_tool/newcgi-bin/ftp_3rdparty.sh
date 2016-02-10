#!/bin/sh
a=`date +%m.%d.%Y --date='1 day ago'`
echo $a
/var/www/html/newcgi-bin/ftp_tranzact.pl $a > /var/www/util/logs/ftp_tranzact_$a.log
# - JES - 06/23 - Paused per Eric - /var/www/html/newcgi-bin/ftp_slks.pl $a > /var/www/util/logs/ftp_3rdparty_$a.log
#/var/www/html/newcgi-bin/ftp_dosmonos2.pl >> /var/www/util/logs/ftp_3rdparty_$a.log
#/var/www/html/newcgi-bin/ftp_brd.pl >> /var/www/util/logs/ftp_3rdparty_$a.log
##/var/www/html/newcgi-bin/ftp_dosmonos.pl >> /var/www/util/logs/ftp_3rdparty_$a.log
/var/www/html/newcgi-bin/ftp_getads.pl >> /var/www/util/logs/ftp_3rdparty_$a.log
## - JES - 06/23 - Paused per Eric - /var/www/html/newcgi-bin/ftp_wing.pl  $a >> /var/www/util/logs/ftp_3rdparty_$a.log
#/var/www/html/newcgi-bin/ftp_intela.pl  $a >> /var/www/util/logs/ftp_3rdparty_$a.log
##/var/www/html/newcgi-bin/ftp_razor.pl $a >> /var/www/util/logs/ftp_3rdparty_$a.log
/var/www/html/newcgi-bin/ftp_qinteractive.pl >> /var/www/util/logs/ftp_3rdparty_$a.log
## - JES - 05/08 - per Eric - /var/www/html/newcgi-bin/ftp_tcomm.pl >> /var/www/util/logs/ftp_3rdparty_$a.log
## - JES - 06/23 - paused per ERIC - /var/www/html/newcgi-bin/ftp_cbs.pl  $a >> /var/www/util/logs/ftp_3rdparty_$a.log
#/var/www/html/newcgi-bin/ftp_ppm.pl $a  >> /var/www/util/logs/ftp_3rdparty_$a.log
## - JES - 11/17 - /var/www/html/newcgi-bin/ftp_raut.pl >> /var/www/util/logs/ftp_3rdparty_$a.log

cd /var/www/util/data/new
mv *$a*.txt /mailfiles/sav
gzip /mailfiles/sav/*$a*.txt
