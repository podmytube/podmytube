#!/bin/bash
# this script will only create some errors to be tested
START_TIME=$SECONDS

if [ -f ~/dotfiles/.bash_functions ]; then
	source ~/dotfiles/.bash_functions
fi

title "Inserting errors to be tested"

if [ -z ${MYSQLSERVER_CREDS} ]; then
	error "Credentials {$MYSQLSERVER_CREDS} for accessing mysqlServer container is empty. You should set a dotfiles/.creds"
	exit 1
fi

notice "Removing subscription for invalidChannel into pmtests (host : mysqlServer)"
mysql -hmysqlServer ${MYSQLSERVER_CREDS} pmtests -e "delete from subscriptions where channel_id='invalidChannel'"
if [ "$?" != "0" ]; then
    error "La suppression de la subscription de la chaine invalidChannel dans la base de test a echoue !"
    exit 1	
fi

notice "Creating false entry into thumbs table "
mysql -hmysqlServer ${MYSQLSERVER_CREDS} pmtests -e "insert into thumbs (channel_id, file_name, file_disk, file_size) values ('freeChannel', 'thumbFileNameThatDontExists.jpg', 'thumbs', '120')"
if [ "$?" != "0" ]; then
    error "L insertion d'un faux thumb dans la base de test a echoue !"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
