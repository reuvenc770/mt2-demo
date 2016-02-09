#!/bin/sh
cd /var/www/util/thumbnail/$2/$3/$4
ftp -n imail1 <<EOF >>/dev/null
user web_ftp 3RatneAR 
cd affiliateimages
cd images
cd thumbnail
mkdir $2
mkdir $2/$3
mkdir $2/$3/$4
cd $2/$3/$4
binary
prompt
mput $1 
bye
EOF
#rm $1
