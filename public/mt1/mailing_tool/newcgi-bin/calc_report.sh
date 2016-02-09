#/bin/sh
export DATABASE=new_mail
export DATABASE_HOST=slavedb.i.routename.com
export DATABASE_USER=db_readuser
export DATABASE_PASSWORD=Tr33Wat3r
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

export MAILTO="mailops@spirevision.com"
#export CALC_CLIENTS="719,717,669,185,6,229,687,644,657,357,585,278,619,706,704,703,124,148,469,685,176,23,652,650,236,607,616,643,202,174,11,15,104,77,598,135,331,92,19,486,654,614,2,24,503,27,246,29,314,696,563,564,479,455,735,738,739,712,723,715,716,714,756,753,743,254,506,370,373,470,697,301,183,683,527,212,589,326,366,262,397"
# - No longer used - export CALC_CLIENTS="719,717,669,185,6,229,687,644,657,357,585,278,619,706,704,703,124,148,469,685,176,23,652,650,236,607,616,643,202,174,11,15,104,77,598,135,331,92,19,486,654,614,2,24,503,27,246,29,314,696,563,564,479,455,735,738,739,712,723,715,716,714,756,753,743,254,506,370,373,470,697,301,183,683,527,212,589,326,366,262,397,47,299,378,396,712,45,672,106,787,788,365,807,523,471,606,577,806,809,803,779,782,743,796,808"
export PERL5LIB=/usr/lib/perl5/site_perl/production:/var/www/bin:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/x86_64-linux-thread-multi:/opt/ecelerity/lib/perl5/vendor_perl/5.8.5/i686-linux-thread-multi:$PERL5LIB
#
a=`date +%m%d%Y`
day=`date +%a`
/var/www/html/newcgi-bin/calc_report2.pl 8396 "$@" >> /var/www/util/logs/calc_$a.log &
/var/www/html/newcgi-bin/calc_report1.pl 4527 "$@" >> /var/www/util/logs/calc_$a.log &
/var/www/html/newcgi-bin/calc_report1.pl 4519 "$@" >> /var/www/util/logs/calc_$a.log &
/var/www/html/newcgi-bin/calc_report1.pl 7308 "$@" >> /var/www/util/logs/calc_$a.log &
/var/www/html/newcgi-bin/calc_report1.pl 8300 "$@" >> /var/www/util/logs/calc_$a.log
/var/www/html/newcgi-bin/calc_report1.pl 8501 "$@" >> /var/www/util/logs/calc_$a.log
/var/www/html/newcgi-bin/calc_report1.pl 1653 "$@" >> /var/www/util/logs/calc_$a.log 
/var/www/html/newcgi-bin/calc_report1.pl 4383 "$@" >> /var/www/util/logs/calc_$a.log &
/var/www/html/newcgi-bin/calc_report1.pl 8506 "$@" >> /var/www/util/logs/calc_$a.log &
/var/www/html/newcgi-bin/calc_report1.pl 8540 "$@" >> /var/www/util/logs/calc_$a.log &
/var/www/html/newcgi-bin/calc_report1.pl 4438 "$@" >> /var/www/util/logs/calc_$a.log
/var/www/html/newcgi-bin/calc_report1.pl 4439 "$@" >> /var/www/util/logs/calc_$a.log 
/var/www/html/newcgi-bin/calc_report.pl > /var/www/util/logs/calc_$a.log
