#! /bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
set a=`date +%m%d%y%H%s`
set b=`date +%m%d%Y`

#find /var/www/util/logs -empty -exec rm -f {} \;
#
cd /var/www/util/src
/usr/bin/perl optin_send_email.pl >> /var/www/util/logs/optin_send_email_$b.log
rm /tmp/a.a
ls /var/www/util/tmpmailfiles/list*mail2* > /tmp/a.a
mv /var/www/util/tmpmailfiles/list_jjdb* /var/www/util/mailfiles
if (-z "/tmp/a.a") then
	echo "No Files"
else
	/var/www/util/src/optin_push_mailfiles.sh >> /var/www/util/logs/push_optin_mailfiles_$b.log
endif
