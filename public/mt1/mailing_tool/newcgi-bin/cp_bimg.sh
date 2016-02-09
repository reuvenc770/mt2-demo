#!/bin/sh
cd /var/www/util/tmp
ftp -n imail1<<EOF>/tmp/j.
user web_ftp1 3RatneAR
binary
prompt
mput $1 
bye
EOF
rm $1
