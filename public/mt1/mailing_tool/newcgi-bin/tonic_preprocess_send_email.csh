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
/usr/bin/perl new_preprocess_send_email.pl 1 >> /var/www/util/logs/tonic_preprocess_send_email_$b.log
rm /tmp/fa_tonic.a
#ls /var/www/util/tmpmailfiles/list_fa_* > /tmp/fa_tonic.a
#if (-z "/tmp/fa_tonic.a") then
#	echo "No Files"
#else
#	/var/www/html/newcgi-bin/new_push_mailfiles.sh >> /var/www/util/logs/new1_push_mailfiles_$b.log
#endif
