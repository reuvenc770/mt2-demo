#!/bin/sh
FSUFFIX=".txt"
COMDIR="common.data"
DIRHOLD="x"
cdate=`date +%m%d%Y%H%s`
OUTFILE="uns_1_${cdate}.dat"
eval LOG="/var/www/util/logs/move_uns.log"
rm -Rf $LOG

#echo "<BEGIN> findfiles.sh at: " `date` > ${LOG}

DIRIN="/home/spam"
cd ${DIRIN}
FILES=`find ${DIRIN} -name "*${FSUFFIX}" -maxdepth 1 -print 2>>${LOG}`

PRINT_DIR="Y"
for i in ${FILES}
do
	FILENAME=`echo ${i} | sed "s:.*\/::"`
    NBRRECS=`cat ${FILENAME} | wc -l`

    if [ ${PRINT_DIR} =  "Y" ]
    then
    	echo "Directory: ${DIRIN}"  >> ${LOG}
        PRINT_DIR="N"
    fi

    echo "    FileName: ${FILENAME}  contains: ${NBRRECS} to process"    >> ${LOG}
	if [ ${NBRRECS} > 0 ]
	then
		ftp -n idbbox <<EOF
user transfer1 18years
cd /var/www/aog/data/uns
ascii
put ${FILENAME} 
bye
EOF
		mv ${FILENAME} /var/www/util/data/uns/${FILENAME}	
	fi

done
#echo "<END>   findfiles.sh at: " `date` >> ${LOG}
cat ${LOG}
