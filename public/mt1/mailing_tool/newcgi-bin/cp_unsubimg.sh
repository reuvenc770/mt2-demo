#!/bin/sh
cd /var/www/util/tmp
ftp -n imail1 <<EOF >>/dev/null
user web_ftp 3RatneAR 
cd affiliateimages
cd images
mkdir $2
EOF
ftp -n imail1 <<EOF >>/dev/null
user web_ftp 3RatneAR 
cd affiliateimages
cd images
cd $2
binary
prompt
mput $1 
bye
EOF
rm $1
