#!/bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 000

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
find /var/www/html/logs -atime +7 -exec rm -f {} \;
find /var/www/html/logs -empty -exec rm -f {} \;
cd /var/www/html/newcgi-bin
set a=`date +%m%d%y%H%M%S`
setenv PERL5LIB /var/www/util/src:/usr/lib/perl5/site_perl/production
/usr/bin/perl add_supplist.pl > ../logs/add_new_supplist_$a.log
