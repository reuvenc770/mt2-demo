#!/bin/sh
cd /data5/supp
ftp -n ftp.aspiremail.com<<EOF
user acxiom mka92jhd 
binary
put MichaelsPermissionPassAcxiom.zip XLMarketingMichaelsDNE.zip
bye
EOF
