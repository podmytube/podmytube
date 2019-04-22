#!/bin/bash
# this script will only create some errors to be tested
START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library

title "Inserting errors to be tested"

if [ -z ${MYSQLSERVER_ROOT_PASSWORD} ]; then
	error "Credentials {${MYSQLSERVER_ROOT_PASSWORD}} for accessing mysqlServer container is empty. It shouldn't ..."
	exit 1
fi

notice "Removing subscription for invalidChannel into pmtests (host : mysqlServer)"
mysql -hmysqlServer -uroot -p${MYSQLSERVER_ROOT_PASSWORD} pmtests -e "delete from subscriptions where channel_id='invalidChannel'"
if [ "$?" != "0" ]; then
    error "La suppression de la subscription de la chaine invalidChannel dans la base de test a echoue !"
    exit 1	
fi

notice "Creating false entry into thumbs table "
mysql -hmysqlServer -uroot -p${MYSQLSERVER_ROOT_PASSWORD} pmtests -e "insert into thumbs (channel_id, file_name, file_disk, file_size) values ('freeChannel', 'thumbFileNameThatDontExists.jpg', 'thumbs', '120')"
if [ "$?" != "0" ]; then
    error "L insertion d'un faux thumb dans la base de test a echoue !"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
