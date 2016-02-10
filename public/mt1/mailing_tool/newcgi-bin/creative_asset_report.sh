#/bin/sh
export DATABASE=new_mail
export DATABASE_HOST=sv-db.routename.com
export DATABASE_USER=db_user
export DATABASE_PASSWORD=sp1r3V
export MASTER_SUPPRESSION_DATABASE=supp
export MASTER_SUPPRESSION_DATABASE_HOST=suppress.routename.com
export MASTER_SUPPRESSION_DATABASE_USER=db_user
export MASTER_SUPPRESSION_DATABASE_PASSWORD=sp1r3V

export SLAVE1_SUPPRESSION_DATABASE=supp
export SLAVE1_SUPPRESSION_DATABASE_HOST=suppressreaddbp.routename.com
export SLAVE1_SUPPRESSION_DATABASE_USER=db_user
export SLAVE1_SUPPRESSION_DATABASE_PASSWORD=sp1r3V

export MAILING_HEADER_BACKUP_FILE=/var/www/util/mailingHeader.txt
export WIKI_BACKUP_FILE=/var/www/util/wiki.txt
export SV_WIKI_CACHE_FILE=/var/www/util/svWiki.txt
export ALERT_EMAIL_ADDRESS=alert.tech@spirevision.com
export ALERT_EMAIL_HOST=localhost
export RECREATE_WIKI_CONTENT_TIME=21400
export TEST=0
export DEBUG=0

export MAILINGDATA_DATABASE=MailingData
export MAILINGDATA_DATABASE_HOST=mailingdatadb.routename.com
export MAILINGDATA_DATABASE_USER=db_user
export MAILINGDATA_DATABASE_PASSWORD=sp1r3V

export UNSUBSCRIBE_DBM_DIR=/var/www/util/data
export UNSUBSCRIBE_DBM_FILE=unsubscribe.dbm
export SUPPRESSION_DIRECTORY=/var/local/programdata/suppression/

export PERL5LIB=/usr/lib/perl5/site_perl/production:/var/www/bin:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/x86_64-linux-thread-multi:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/i686-linux-thread-multi:$PERL5LIB
rdate=`date +%m-%d-%Y`
perl /var/www/html/newcgi-bin/creative_asset_report.pl "$@" >> /var/www/util/logs/creative_asset_$rdate.log 2>&1
