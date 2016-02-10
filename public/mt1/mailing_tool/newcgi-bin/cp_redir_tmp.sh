#!/bin/sh
a=`date +%m%d%Y`
chmod 777 /var/www/html/logs/redir.dat
/var/www/html/newcgi-bin/ftp_redir_tmp.pl >> /var/www/util/logs/tmp_push_redir_$a.log 2>&1
