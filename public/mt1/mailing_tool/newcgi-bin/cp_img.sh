#!/bin/sh
a=`date +%m%d%Y`
cd /var/www/util/tmpimg/$1
echo "" >> /tmp/img_$a.log
echo "Directory $1" >> /tmp/img_$a.log
ls -l >> /tmp/img_$a.log
ftp -n imail1 <<EOF>>/tmp/img_$a.log
user web_ftp 3RatneAR 
verbose
cd affiliateimages
cd images
mkdir $1
EOF
ftp -n imail1 <<EOF >>/tmp/img_$a.log
user web_ftp 3RatneAR 
verbose
cd affiliateimages
cd images
cd $1
binary
prompt
mput *
ls
bye
EOF
rm * > /dev/null 2>&1
echo "" >> /tmp/img_$a.log
exit 0
