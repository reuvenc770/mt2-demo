#!/bin/sh
cd /data3/supp
/usr/bin/unzip -d temp1 -q $1.zip > /dev/null 2>&1
cd temp1
mv $1* $1UL.txt > /dev/null 2>&1
