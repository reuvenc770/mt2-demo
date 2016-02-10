#! /bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin /home/leaddog/bin)
set a=`date +%m%d%y%H%s`

#find /var/www/util/logs -empty -exec rm -f {} \;
find /tmp -atime +1 -exec rm -f {} \;
find /home/tmp -atime +1 -exec rm -f {} \;
find /var/www/util/logs -atime +7 -exec rm -f {} \;
find /var/www/util/mailfiles/working -atime +1 -exec rm -f {} \;
#
cd /var/www/util/src
#rm /var/www/util/logs/batch*.log
rm /tmp/batch_send.log
rm /tmp/batch_send1.log
df -k /var | grep dev | awk '{ if (int(substr($5,0,2)) < 90) printf "Run %s \n",substr($5,0,2); }' > /tmp/batch_send.log
df -i /var | grep dev | awk '{ if (int(substr($5,0,2)) < 60) printf "Run %s \n",substr($5,0,2); }' > /tmp/batch_send1.log
if ((-s "/tmp/batch_send.log") && (-s "/tmp/batch_send1.log")) then
/usr/bin/perl fast_batch_send_email.pl > /var/www/util/logs/batch_send_email_$a.log 
endif
