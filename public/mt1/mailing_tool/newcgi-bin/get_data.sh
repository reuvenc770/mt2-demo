#!/bin/sh

export PERL5LIB=/var/www/html/newcgi-bin:/usr/lib/perl5/site_perl/production
export FLATFILE_SRC=/var/www/util
export FLATFILE_DEST=/var/www/util/data
export FLATFILE_DEST_SERVER=mtadata03.routename.com
export MAX_ACCEPTABLE_LOCK_AGE=500000

export DATABASE=new_mail
export DATABASE_HOST=masterdb.routename.com
export DATABASE_USER=db_user
export DATABASE_PASSWORD=sp1r3V

adate=`date +%y%m%d`

cd /var/www/html/newcgi-bin
/usr/bin/perl get_data.pl "$@" >> /var/www/util/logs/get_data_$adate.log
#/usr/bin/perl get_data.pl "$@" &> /tmp/get_data.log
