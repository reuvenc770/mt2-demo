#!/bin/sh
cd /var/lib/mysql/tmpsupp
rm -f $2*
split -l40000 $1 $2
