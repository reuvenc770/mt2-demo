#!/bin/sh
#
#   Do non-NHI serves
#
MAILSRV="imail7 imail8 imail9 imail11 imail2 imail4 imail5 imail6 imail10 imail3 cybercon1 webstream cybercon2 inv1 inv2"
for SRV in ${MAILSRV}
do
	/var/www/html/newcgi-bin/push.sh ${SRV} &	
#
done
rm /tmp/fa2.log
cd /var/www/util/new_tmpmailfiles
ftp db2 << EOF
verbose
cd /var/www/tonic/mailfiles
ascii
prompt off
mput list_fa_db2_*
ls list_fa_db2_* /tmp/fa2.log
bye
EOF
if [ -r "/tmp/fa2.log" ]
then
if [ -z "/tmp/fa2.log" ]
then
    echo "Subject: FTP Failure FA Db2}" >> /tmp/fa2.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa2.log
    /usr/lib/sendmail -t < /tmp/fa2.log
    mv list_fa_db2_* sav
else
rm list_fa_db2_*
fi
else
    echo "Subject: FTP Failure FA Db2}" >> /tmp/fa2.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa2.log
    /usr/lib/sendmail -t < /tmp/fa2.log
    mv list_fa_db2_* sav
fi
rm /tmp/fa3.log
cd /var/www/util/new_tmpmailfiles
ftp db1 << EOF
verbose
cd /var/www/peaks/mailfiles
ascii
prompt off
mput list_fa_db1_*
ls list_fa_db1_* /tmp/fa3.log
bye
EOF
if [ -r "/tmp/fa3.log" ]
then
if [ -z "/tmp/fa3.log" ]
then
    echo "Subject: FTP Failure FA Db1}" >> /tmp/fa3.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa3.log
    /usr/lib/sendmail -t < /tmp/fa3.log
    mv list_fa_db1_* sav
else
rm list_fa_db1_*
fi
else
    echo "Subject: FTP Failure FA Db1}" >> /tmp/fa3.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa3.log
    /usr/lib/sendmail -t < /tmp/fa3.log
    mv list_fa_db1_* sav
fi
rm /tmp/fa1.log
cd /var/www/util/new_tmpmailfiles
ftp ispire003 << EOF
verbose
cd /var/www/tonic/mailfiles
ascii
prompt off
mput list_fa_ispire003_*
ls list_fa_ispire003_* /tmp/fa1.log
bye
EOF
if [ -r "/tmp/fa1.log" ]
then
if [ -z "/tmp/fa1.log" ]
then
    echo "Subject: FTP Failure FA ispire003}" >> /tmp/fa1.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa1.log
    /usr/lib/sendmail -t < /tmp/fa1.log
    mv list_fa_ispire003_* sav
else
rm list_fa_ispire003_*
fi
else
    echo "Subject: FTP Failure FA ispire003}" >> /tmp/fa1.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa1.log
    /usr/lib/sendmail -t < /tmp/fa1.log
    mv list_fa_ispire003_* sav
fi
rm /tmp/fa1.log
cd /var/www/util/new_tmpmailfiles
ftp ispire003 << EOF
verbose
cd /var/www/primeq/mailfiles
ascii
prompt off
mput list_fa_ispire003p_*
ls list_fa_ispire003p_* /tmp/fa1.log
bye
EOF
if [ -r "/tmp/fa1.log" ]
then
if [ -z "/tmp/fa1.log" ]
then
    echo "Subject: FTP Failure FA ispire003}" >> /tmp/fa1.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa1.log
    /usr/lib/sendmail -t < /tmp/fa1.log
    mv list_fa_ispire003p_* sav
else
rm list_fa_ispire003p_*
fi
else
    echo "Subject: FTP Failure FA ispire003}" >> /tmp/fa1.log
    echo "To: jsobeck@lead-dog.net" >> /tmp/fa1.log
    /usr/lib/sendmail -t < /tmp/fa1.log
    mv list_fa_ispire003p_* sav
fi
