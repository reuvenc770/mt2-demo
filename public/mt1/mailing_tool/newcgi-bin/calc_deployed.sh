#/bin/sh
export DATABASE=new_mail
export DATABASE_HOST=slavedb.routename.com
export DATABASE_USER=db_user
export DATABASE_PASSWORD=sp1r3V
export MASTER_SUPPRESSION_DATABASE=supp
export MASTER_SUPPRESSION_DATABASE_HOST=suppress.routename.com
export MASTER_SUPPRESSION_DATABASE_USER=db_user
export MASTER_SUPPRESSION_DATABASE_PASSWORD=sp1r3V

export SLAVE1_SUPPRESSION_DATABASE=supp
export SLAVE1_SUPPRESSION_DATABASE_HOST=suppressreaddbp.routename.com
export SLAVE1_SUPPRESSION_DATABASE_USER=db_readuser
export SLAVE1_SUPPRESSION_DATABASE_PASSWORD=Tr33Wat3r

export MAILING_HEADER_BACKUP_FILE=/var/www/util/mailingHeader.txt
export WIKI_BACKUP_FILE=/var/www/util/wiki.txt
export SV_WIKI_CACHE_FILE=/var/www/util/svWiki.txt
export ALERT_EMAIL_ADDRESS=alert.tech@spirevision.com
export RECREATE_WIKI_CONTENT_TIME=21400
export TEST=0

export PERL5LIB=/usr/lib/perl5/site_perl/production:/var/www/bin:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/x86_64-linux-thread-multi:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/i686-linux-thread-multi:$PERL5LIB
#
# Remove all old sorted files
#
find /var/www/util/data -maxdepth 1 -mtime +0 -name "*.sorted" -exec rm -f {} \; > /dev/null 2>&1
find /var/www/util/data -maxdepth 1 -mtime +0 -name "*.txt" -exec rm -f {} \; > /dev/null 2>&1
rdate=`date +%m-%d-%Y`
/var/www/html/newcgi-bin/calc_deployed.pl "$@" >> /tmp/calc_error_$rdate.log 2>&1
