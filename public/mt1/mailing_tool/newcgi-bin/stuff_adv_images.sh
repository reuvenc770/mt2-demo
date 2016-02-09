#!/bin/sh
if [ -d "/data5/hitpath/$1" ]; then
cd /data5/hitpath/$1
rm ../$2.zip > /dev/null 2>&1
/usr/local/bin/stuff -f=zip -n=../$2.zip * > /dev/null 2>&1
rm -Rf * > /dev/null 2>&1
rmdir /data5/hitpath/$1
fi
