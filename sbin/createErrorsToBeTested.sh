#!/bin/bash
# this script will only create some errors to be tested
START_TIME=$SECONDS

if [ -f ~/dotfiles/.bash_functions ]; then
	source ~/dotfiles/.bash_functions
fi

title "Inserting errors to be tested"

if [ -z "${PMTESTDB_CREDS}" ]; then
	error "Test database credentials {$PMTDB_CREDS} is empty. It shouldn't ..."
	exit 1
fi

if [ -z ${PMTEST_DB} ] || [ -z ${PMTEST_HOST} ]; then
	error "Either test database name {$PMTEST_DB} or host {$PMTEST_HOST} is empty. It shouldn't ..."
	exit 1
fi

notice "Removing subscription for invalidChannel into ${PMTEST_DB} (host : ${PMTEST_HOST})"
mysql -h${PMTEST_HOST} ${PMTESTDB_CREDS} ${PMTEST_DB} -e "delete from subscriptions where channel_id='invalidChannel'"
if [ "$?" != "0" ]; then
    error "La suppression de la subscription de la chaine invalidChannel dans la base de test a echoue !"
    exit 1	
fi

notice "Creating false entry into thumbs table "
mysql -h${PMTEST_HOST} ${PMTESTDB_CREDS} ${PMTEST_DB} -e "insert into thumbs (channel_id, file_name, file_disk, file_size) values ('freeChannel', 'thumbFileNameThatDontExists.jpg', 'thumbs', '120')"
if [ "$?" != "0" ]; then
    error "L insertion d'un faux thumb dans la base de test a echoue !"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
