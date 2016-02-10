#!/bin/sh
rm /tmp/fa_$1.log
cd /var/www/util/new_tmpmailfiles1
ftp $1 << EOF
verbose
cd /var/www/util/mailfiles
ascii
prompt off
mput list_fa_$1_*
ls list_fa_$1_* /tmp/fa_$1.log
bye
EOF
if [ -r "/tmp/fa_$1.log" ]
then
if [ -z "/tmp/fa_$1.log" ]
then
    echo "Subject: FTP Failure FA $1" >> /tmp/fa_$1.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa_$1.log
    /usr/lib/sendmail -t < /tmp/fa_$1.log
    mv list_fa_$1_* sav
else
rm list_fa_$1_*
fi
else
    echo "Subject: FTP Failure FA $1" >> /tmp/fa_$1.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa_$1.log
    /usr/lib/sendmail -t < /tmp/fa_$1.log
    mv list_fa_$1_* sav
fi
