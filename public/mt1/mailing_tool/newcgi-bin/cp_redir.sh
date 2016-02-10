#!/bin/sh
a=`date +%m%d%Y`
/var/www/html/newcgi-bin/ftp_redir.pl >> /var/www/util/logs/push_redir_$a.log 2>&1
