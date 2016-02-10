#! /bin/csh
set a=`date +%m%d%y`
#cat $1 >> /home/unsubs/uns_$a.txt
cp $1 /var/www/cp/src
cp $1 /var/www/jumpjive/src
cp $1 /var/www/livenation/src
cp $1 /var/www/master/src
cd /var/www/cp/src
./sub_uns.pl $1 
cd /var/www/jumpjive/src
./sub_uns.pl $1 
cd /var/www/livenation/src
./sub_uns.pl $1 
cd /var/www/master/src
./sub_uns.pl $1 
cd /var/www/util/src
./sub_uns.pl $1
