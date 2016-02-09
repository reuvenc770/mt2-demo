#!/bin/sh
a=`date +%m%d%y`
cd /var/www/html/newcgi-bin
/usr/bin/perl kenzei_unsub.pl $a
cd /tmp
/usr/local/bin/stuff -f=zip kenzei_unsubs_$a.txt
rm kenzei_unsubs_$a.txt
ftp -n kzftp.kenzei.net<<EOF
user spirevision enterthepuggle
cd unsubs
binary
put kenzei_unsubs_$a.txt.zip
bye
EOF
