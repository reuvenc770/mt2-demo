#!/bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin)
set a=`date +%m%d%y%H%s`
set b=`date +%m%d%Y`

#
cd /var/www/html/newcgi-bin
/usr/bin/perl new_preprocess_send_email.pl 18 >> /var/www/util/logs/webclients_preprocess_send_email_$b.log
