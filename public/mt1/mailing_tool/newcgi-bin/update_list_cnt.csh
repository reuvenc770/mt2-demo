#!/bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'
set b=`date +%m%d%Y`

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
#find /var/www/util/logs -empty -exec rm -f {} \;
cd /var/www/html/newcgi-bin
/usr/bin/perl update_list_cnt.pl > /var/www/util/logs/newtool_update_list_cnt_$b.log
