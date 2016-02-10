#! /bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
set a=`date +%m%d%y%H%s`

find /var/www/pms/logs -empty -exec rm -f {} \;
#
cd /var/www/pms/src
/usr/bin/perl preprocess_special.pl > /var/www/pms/logs/preprocess_special.log
mv /var/www/pms/tmpmailfiles/* /var/www/pms/mailfiles
