#! /bin/csh
limit coredumpsize 0
set watch = (0 any any)
set who = "%B%n%b %a %M(%l) %@"
umask 022

setenv BLOCKSIZE '1k'
setenv LD_LIBRARY_PATH '/usr/lib:/usr/local/lib'

set path=(/usr/local/bin /bin /usr/bin /usr/X11R6/bin)

#cd /var/www/offersinc/src
#/usr/bin/perl upd_schedule.pl >> /var/www/util/logs/upd_schedule.log
cd /var/www/jumpjive/src
/usr/bin/perl upd_schedule1.pl >> /var/www/util/logs/upd_schedule.log
cd /var/www/sweepsinc/src
/usr/bin/perl upd_schedule1.pl >> /var/www/util/logs/upd_schedule.log
cd /var/www/livenation/src
/usr/bin/perl upd_schedule1.pl >> /var/www/util/logs/upd_schedule.log
cd /var/www/util/src
/usr/bin/perl upd_schedule1.pl >> /var/www/util/logs/upd_schedule.log
/usr/bin/perl build_schedule_info.pl >> /var/www/util/logs/upd_schedule.log
