#!/bin/csh
limit coredumpsize 0
set adate=`date +%m%d%Y`
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

setenv PERL5LIB /var/www/html/newcgi-bin:/usr/lib/perl5/site_perl/production
setenv FLATFILE_SRC /var/www/util
setenv FLATFILE_DEST /var/www/util/data
setenv FLATFILE_DEST_SERVER sv-db-14.pcposts.com
setenv MAX_ACCEPTABLE_LOCK_AGE 500000

cd /var/www/html/newcgi-bin
/usr/bin/perl sv_get_data.pl >> /var/www/util/logs/sv_get_data_$adate.log
#/usr/bin/perl sv_get_data.pl 

