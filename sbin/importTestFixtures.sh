#!/bin/bash
# this script will only import sample tests data

START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library

title "Importing tests fixtures"

if [ -z ${MYSQLSERVER_ROOT_PASSWORD} ]; then
	error "Credentials {${MYSQLSERVER_ROOT_PASSWORD}} for accessing mysqlServer container is empty. It shouldn't ..."
	exit 1
fi

FIXTURE_FILE="${__DIR__}/../tests/fixtures/datasets/SampleChannelsMediasAndSubscriptions.sql"

notice "importing fixture data into pmtests (host : mysqlServer)"
mysql -hmysqlServer -uroot -p${MYSQLSERVER_ROOT_PASSWORD} pmtests < $FIXTURE_FILE 
if [ "$?" != "0" ]; then
    echo "L importation des donnees de test dans la base de test a echoue !"
    exit 1	
fi

# copying sample thumb in easychannel thumb
SAMPLE_THUMB_FILE="${__DIR__}/../tests/fixtures/images/sampleThumb.jpg"
SAMPLE_THUMB_FOLDER="${__DIR__}/../storage/app/public/thumbs/earlyChannel/"
if [ ! -d ${SAMPLE_THUMB_FOLDER} ]; then
	mkdir ${SAMPLE_THUMB_FOLDER} 
fi

cp ${SAMPLE_THUMB_FILE} ${SAMPLE_THUMB_FOLDER} && chown -R www-data:www-data ${SAMPLE_THUMB_FOLDER}
if [ "$?" != "0" ]; then
    echo "La copie du sample thumb a echoue !"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
