#!/bin/bash
# this script will only import sample tests data

START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library

title "Importing tests fixtures"

# getting info from .env.testing file
ENV_FILE=".env.testing"
DST_DB_USER=$(read_var DB_USERNAME ${ENV_FILE})
DST_DB_PASS=$(read_var DB_PASSWORD ${ENV_FILE})
DST_DB_NAME=$(read_var DB_DATABASE ${ENV_FILE})
DST_DB_HOST=$(read_var DB_HOST ${ENV_FILE})

CONNECTION_PARAMS="-h${DST_DB_HOST} -u${DST_DB_USER} -p${DST_DB_PASS} ${DST_DB_NAME} "

FIXTURE_FILE="${__DIR__}/../tests/fixtures/datasets/SampleChannelsMediasAndSubscriptions.sql"

notice "importing fixture data into ${DST_DB_NAME} (host : mysqlServer)"
mysql ${CONNECTION_PARAMS} < $FIXTURE_FILE 
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
