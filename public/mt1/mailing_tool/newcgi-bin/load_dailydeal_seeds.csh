#!/bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin)
set b=`date +%m%d%Y`

#
cd /var/www/html/newcgi-bin
/usr/bin/perl load_dailydeal_seeds.pl "$@" >> /var/www/util/logs/load_dailydealseeds_$b.log 
chmod 777 /var/www/util/logs/load_dailydealseeds_$b.log
