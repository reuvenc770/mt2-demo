#!/bin/sh
cd /data5/hitpath/$1
rm ../$1.zip > /dev/null 2>&1
/usr/local/bin/stuff -f=zip -n=../$1.zip * > /dev/null 2>&1
rm -Rf * > /dev/null 2>&1
rmdir /data5/hitpath/$1
