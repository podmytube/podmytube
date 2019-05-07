#!/bin/bash
# this script will only create some errors to be tested
START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library

title "Inserting errors to be tested"

# getting info from .env.testing file
ENV_FILE=".env.testing"
DST_DB_USER=$(read_var DB_USERNAME ${ENV_FILE})
DST_DB_PASS=$(read_var DB_PASSWORD ${ENV_FILE})
DST_DB_NAME=$(read_var DB_DATABASE ${ENV_FILE})
DST_DB_HOST=$(read_var DB_HOST ${ENV_FILE})

CONNECTION_PARAMS="-h${DST_DB_HOST} -u${DST_DB_USER} -p${DST_DB_PASS} ${DST_DB_NAME}"

notice "Removing subscription for invalidChannel into ${DST_DB_NAME} (host : mysqlServer)"
mysql ${CONNECTION_PARAMS} -e "delete from subscriptions where channel_id='invalidChannel'"
if [ "$?" != "0" ]; then
    error "La suppression de la subscription de la chaine invalidChannel dans la base de test a echoue !"
    exit 1	
fi

notice "Creating false entry into thumbs table "
mysql ${CONNECTION_PARAMS} -e "insert into thumbs (channel_id, file_name, file_disk, file_size) values ('freeChannel', 'thumbFileNameThatDontExists.jpg', 'thumbs', '120')"
if [ "$?" != "0" ]; then
    error "L insertion d'un faux thumb dans la base de test a echoue !"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
