#!/bin/sh
FSUFFIX="uns"
COMDIR="common.data"
DIRHOLD="x"
cdate=`date +%m%d%Y%H%s`
OUTFILE="uns_1_${cdate}.dat"
eval LOG="/var/www/util/logs/procuns.log"
rm -Rf $LOG

#echo "<BEGIN> findfiles.sh at: " `date` > ${LOG}

DIRIN="/var/www/util/data/uns/"
cd ${DIRIN}
FILES=`find ${DIRIN} -name "${FSUFFIX}*" -maxdepth 1 -print 2>>${LOG}`

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
		cp ${FILENAME} /var/www/util/src/${FILENAME}	
		cd ${DIRIN}
		mv ${FILENAME} working
		cd /var/www/util/src
		./uns.sh ${FILENAME}
		cd ${DIRIN}
		cd working
		mv ${FILENAME} /home/jsobeck/suppress_list.txt
		cd /home/jsobeck
		./load_suppress.sh
		cd ${DIRIN}
	fi

done

FSUFFIX=".txt"
DIRIN="/var/www/util/data/uns/"
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
		cp ${FILENAME} /var/www/util/src/${FILENAME}	
		cd ${DIRIN}
		mv ${FILENAME} working
		cd /var/www/util/src
		./uns.sh ${FILENAME}
		cd ${DIRIN}
		cd working
		mv ${FILENAME} /home/jsobeck/suppress_list.txt
		cd /home/jsobeck
		./load_suppress.sh
		cd ${DIRIN}
	fi

done
#echo "<END>   findfiles.sh at: " `date` >> ${LOG}
cat ${LOG}
