#!/bin/sh
cd /tmp/$1
rm ../${3}++${2}++${1}++${4}.zip > /dev/null 2>&1
/usr/local/bin/stuff -f=zip -n=../${3}++${2}++${1}++${4}.zip * > /dev/null 2>&1
rm -Rf * > /dev/null 2>&1
rmdir /tmp/$1
