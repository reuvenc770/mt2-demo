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

## DEFAULT EXPIRY: 30 days for suppression
export DATABASE_CACHE_DEFAULT_EXPIRY=2592000
export DATABASE_CACHE_IO_TIMEOUT=.25
## The weight of each machine should correspond to the GB of memory allocated to memcached on that box
export DATABASE_CACHE_SERVERS="address=mailcache01.routename.com,weight=2;address=mailcache02.routename.com,weight=2;address=mailcache03.routename.com,weight=2;address=mailcache04.routename.com,weight=2;address=mailcache05.routename.com,weight=2;address=mailcache06.routename.com,weight=2;address=mailcache07.routename.com,weight=2;address=mailcache08.routename.com,weight=1;address=mailcache09.routename.com,weight=2;address=mailcache10.routename.com,weight=2"
export DATABASE_CACHE_DISABLE_READ=0
export DATABASE_CACHE_DISABLE_WRITE=0

export UNSUBSCRIBE_DBM_DIR=/var/www/util/data
export UNSUBSCRIBE_DBM_FILE=unsubscribe.dbm
export SUPPRESSION_DIRECTORY=/var/local/programdata/suppression/

export PERL5LIB=/usr/lib/perl5/site_perl/production:/var/www/bin:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/x86_64-linux-thread-multi:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/i686-linux-thread-multi:$PERL5LIB
rdate=`date +%m-%d-%Y`
#perl testServer.pl "$@"
perl /var/www/html/newcgi-bin/bldIpGroup.pl "$@" >> /var/www/util/logs/bldIP_$rdate.log 2>&1
