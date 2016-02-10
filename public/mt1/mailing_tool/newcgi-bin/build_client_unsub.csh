#!/bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
set adate = `date +%m%d%y`
cd /var/www/html/newcgi-bin
/usr/bin/perl build_client_unsub.pl 2 unsubs_NBpeaksnetwork > /var/www/util/logs/build_unsub.log
/usr/bin/perl build_client_unsub.pl 3 unsubs_primeq >> /var/www/util/logs/build_unsub.log
cd /var/www/util/logs
ftp -n imail1 <<EOF
user unsubs 18years 
ascii
prompt
cd peaksnetwork
mput unsubs_NBpeaksnetwork_*.txt
cd ..
cd primeq
mput unsubs_primeq_*.txt
ls
EOF
mv unsubs_NBpeaksnetwork_*.txt /data1/backup
mv unsubs_primeq_*.txt /data1/backup
cd /data1/backup
gzip unsubs_NBpeaksnetwork_*.txt
gzip unsubs_primeq_*.txt
