#!/bin/sh
cd /tmp/$1
rm ../${2}_${3}_${4}_${5}.zip > /dev/null 2>&1
/usr/local/bin/stuff -f=zip -n=../${2}_${3}_${4}_${5}.zip * > /dev/null 2>&1
rm -Rf * > /dev/null 2>&1
rmdir /tmp/$1
