#!/bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
set a=`date +%m%d%Y`
cd /var/www/html/newcgi-bin
/usr/bin/perl move_newrecords.pl > /var/www/util/logs/move_newrecords_$a.log
