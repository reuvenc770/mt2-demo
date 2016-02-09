#! /bin/sh
ftp -n $1 <<EOF
user $2 $3
ascii
put $4 $5
EOF
