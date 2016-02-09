/var/www/html/newcgi-bin/test_spam_new.pl $1 $2 $3 $4 raymond@spirevision.com $5
spamassassin -L < /home/tmp/mesg_spam_$1.dat > /var/www/util/logs/spam_www_results.txt
