#!/bin/sh
a=`date +%Y-%m-%d`
find /home/supp -name "2*" -mtime +7 -exec rm -Rf {} \; > /dev/null 2>&1
mkdir /home/supp/$a
chmod -R 777 /home/supp/$a
