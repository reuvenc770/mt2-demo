#!/bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 000

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
cd /var/www/html/newcgi-bin
set a=`date +%m%d%y%H%M%S`
/usr/bin/perl remove_domains.pl > ../logs/remove_domains_$a.log
