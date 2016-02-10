#!/bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'
set a=`date +%y%m%d`

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'
setenv PERL5LIB /var/www/util/src:/usr/lib/perl5/site_perl/production
setenv DATABASE new_mail
setenv DATABASE_HOST masterdb.routename.com
setenv DATABASE_USER db_user
setenv DATABASE_PASSWORD sp1r3V
setenv MASTER_DATABASE_HOST masterdb.routename.com
setenv MASTER_DATABASE_USER db_user
setenv MASTER_DATABASE_PASSWORD sp1r3V
setenv SUPP_DATABASE supp
setenv WRITE_DATABASE_HOST masterdb.routename.com
setenv SUPP_DATABASE_HOST suppress.routename.com
setenv UNIQUE_DATABASE_HOST suppress.routename.com
setenv NOTIFY_EMAIL alert.tech@spirevision.com
setenv ALERT_EMAIL alert.tech@spirevision.com

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
cd /var/www/html/newcgi-bin
/usr/bin/perl move_clickers.pl >>& /var/www/util/logs/move_clickers_$a.log 
/usr/bin/perl move_openers.pl >>& /var/www/util/logs/move_openers_$a.log
/usr/bin/perl update_clickers.pl >> /var/www/util/logs/update_clickers_$a.log
/usr/bin/perl update_openers.pl >> /var/www/util/logs/update_openers_$a.log
