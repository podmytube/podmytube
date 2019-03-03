#!/bin/bash
# This script will run getAudio test
START_TIME=$SECONDS


case $1 in
	1) CHANNEL='freeChannel' ;;
	2) CHANNEL='earlyChannel' ;;
	3) CHANNEL='weeklyChannel' ;;
	4) CHANNEL='dailyChannel' ;;
	*) CHANNEL='';;
esac



if [ -z ${PMT_DB} ]; then
	echo "PMT database name is not in the environment variable. It should ..."
	exit 1
fi

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# reset test db struct
CMD="$__DIR__/createTestDatabase.sh"
$CMD
if [ "$?" != "0" ]; then
    echo "La creation de la base de donnees de test a echouee !"
    exit 1	
fi


# import test sample into test db
CMD="$__DIR__/importTestFixtures.sh"
$CMD
if [ "$?" != "0" ]; then
    echo "L'importation des donnees de test a echoue !"
    exit 1	
fi

# run getAudio 
CMD="$__DIR__/../bin/getAudio.php --test --verbose"
if [ ! -z ${CHANNEL} ]; then 
	CMD="$CMD --channel $CHANNEL"
fi
$CMD
if [ "$?" != "0" ]; then
    echo "Le traitement a echoue"
    exit 1	
fi


ELAPSED_TIME=$(($SECONDS - $START_TIME))
echo "script duration : ${ELAPSED_TIME}sec"
