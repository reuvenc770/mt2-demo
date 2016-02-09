#!/bin/sh
if [ -d "/data5/hitpath/$1" ]; then
cd /data5/hitpath/$1
$crid $aid $send_date $cdate
rm ../${3}++${2}++${1}++${4}.zip > /dev/null 2>&1
/usr/local/bin/stuff -f=zip -n=../${3}++${2}++${1}++${4}.zip * > /dev/null 2>&1
rm -Rf * > /dev/null 2>&1
rmdir /data5/hitpath/$1
fi
