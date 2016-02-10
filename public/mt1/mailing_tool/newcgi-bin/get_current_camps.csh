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
/usr/bin/perl get_current_camps.pl >> /var/www/util/logs/get_current_camps_$b.log
/usr/bin/perl check_advertiser_links.pl >> /var/www/util/logs/check_advertiser_links_$b.log
/usr/bin/perl check_unused_assets.pl >> /var/www/util/logs/check_unused_assets_$b.log
