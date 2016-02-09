#!/bin/sh
# Copy to Tranzact
cd /data4/data/unsubs
ftp -n data.bloosky.com<<EOF
user spunsub &h%%trERc 
ascii
put uns_$1.txt
ls uns_$1.txt
bye
EOF
#ftp -n 66.109.21.234<<EOF
#user spirevision yqCgPAUH
#cd unsubs
#ascii
#put uns_$1.txt
#ls uns_$1.txt
#bye
#EOF
#
# copy to razor
#
ftp -n www.razormedia.net<<EOF
user miked mike 
cd unsubs
ascii
put uns_$1.txt
ls uns_$1.txt
bye
EOF
#
# copy to ppm
#
ftp -n ppmorl05.precisionplay.com<<EOF
user SV Gl204#20^6
cd users/SV/unsubs
ascii
put uns_$1.txt
ls uns_$1.txt
bye
EOF
#
# copy to dosmonos
#
ftp -n ftp.aspiremail.com<<EOF
user dosmonos 3aReNeXT 
cd unsubs
ascii
put uns_$1.txt
ls uns_$1.txt
bye
EOF
#
# copy to intela 
#
ftp -n ftp.aspiremail.com<<EOF
user intela XPlewtVC 
cd unsubs
ascii
put uns_$1.txt
ls uns_$1.txt
bye
EOF
