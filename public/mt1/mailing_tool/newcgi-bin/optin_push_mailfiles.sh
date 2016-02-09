#! /bin/sh
#
cd /var/www/util/tmpmailfiles
ftp imail1 << EOF
verbose
cd /var/www/util/mailfiles
ascii
prompt off
mput list_mail1_*
bye
EOF
rm list_mail1_*
#
cd /var/www/util/tmpmailfiles
ftp imail2 << EOF
verbose
cd /var/www/util/mailfiles
ascii
prompt off
mput list_mail2*
bye
EOF
rm list_mail2*
#
cd /var/www/util/tmpmailfiles
ftp imail3 << EOF
verbose
cd /var/www/util/mailfiles
ascii
prompt off
mput list_mail3*
bye
EOF
rm list_mail3*
#
cd /var/www/util/tmpmailfiles
ftp imail4 << EOF
verbose
cd /var/www/util/mailfiles
ascii
prompt off
mput list_mail4*
bye
EOF
rm list_mail4*
#
