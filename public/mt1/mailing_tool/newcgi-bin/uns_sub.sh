#!/bin/sh
cd /var/www/util/src
mysql -p483298 -s util < uns_sub.sql > /var/www/util/data/uns/uns_99.dat
