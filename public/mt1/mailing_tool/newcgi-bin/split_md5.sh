#!/bin/sh
cd /var/lib/mysql/tmp
rm -f *
split -l100000 $1 $2
