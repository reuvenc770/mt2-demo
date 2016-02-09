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
/usr/bin/perl preprocess_send_email.pl >> /var/www/util/logs/new_preprocess_send_email_$b.log
rm /tmp/fa.a
#ls /var/www/util/new_tmpmailfiles/list_fa_* > /tmp/fa.a
#if (-z "/tmp/fa.a") then
#	echo "No Files"
#else
#	/var/www/html/newcgi-bin/push_mailfiles.sh >> /var/www/util/logs/new_push_mailfiles_$b.log
#endif
