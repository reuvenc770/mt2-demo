#!/bin/sh
cd /data3/3rdparty/$1
rm ../$1.zip > /dev/null 2>&1
/usr/local/bin/stuff -f=zip -n=../$1.zip * > /dev/null 2>&1
rm -Rf * > /dev/null 2>&1
rmdir /data3/3rdparty/$1
