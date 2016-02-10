/var/www/html/newcgi-bin/get_camp_new.pl $1
/var/www/html/newcgi-bin/test_spam_new.pl $1 $2 $3 $4
spamassassin -L < /home/tmp/mesg_spam_$1.dat > /var/www/util/logs/spam_results_$1.txt
/usr/sbin/sendmail -t < /var/www/util/logs/spam_results_$1.txt
