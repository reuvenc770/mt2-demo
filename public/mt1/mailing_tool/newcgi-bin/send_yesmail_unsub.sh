#/bin/sh
export DATABASE=new_mail
export DATABASE_HOST=slavedb.routename.com
export DATABASE_USER=db_user
export DATABASE_PASSWORD=sp1r3V
export MASTER_SUPPRESSION_DATABASE=supp
export MASTER_SUPPRESSION_DATABASE_HOST=sv-db-5.routename.com
export MASTER_SUPPRESSION_DATABASE_USER=db_user
export MASTER_SUPPRESSION_DATABASE_PASSWORD=sp1r3V

export SLAVE1_SUPPRESSION_DATABASE=supp
export SLAVE1_SUPPRESSION_DATABASE_HOST=sv-db-4.routename.com
export SLAVE1_SUPPRESSION_DATABASE_USER=db_user
export SLAVE1_SUPPRESSION_DATABASE_PASSWORD=sp1r3V

export MAILING_HEADER_BACKUP_FILE=/var/www/util/mailingHeader.txt
export WIKI_BACKUP_FILE=/var/www/util/wiki.txt
export SV_WIKI_CACHE_FILE=/var/www/util/svWiki.txt
export ALERT_EMAIL_ADDRESS=alert.tech@spirevision.com
export RECREATE_WIKI_CONTENT_TIME=21400
export TEST=0

export PERL5LIB=/usr/lib/perl5/site_perl/production:/var/www/bin:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/x86_64-linux-thread-multi:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/i686-linux-thread-multi:$PERL5LIB
#
a=`date +%m%d%Y`
/var/www/html/newcgi-bin/send_yesmail_unsub.pl >> /var/www/util/logs/send_yesmail_unsub_$a.log 2>&1 
