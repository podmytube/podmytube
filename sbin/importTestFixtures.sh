#!/bin/bash
# this script will only import sample tests data

START_TIME=$SECONDS

if [ -f ~/dotfiles/.bash_functions ]; then
	source ~/dotfiles/.bash_functions
fi

title "Importing tests fixtures"

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

if [ -z "${PMTESTDB_CREDS}" ]; then
	error "Test database credentials {$PMTDB_CREDS} are empty. It shouldn't ..."
	exit 1
fi

if [ -z ${PMTEST_DB} ] || [ -z ${PMTEST_HOST} ]; then
	error "Either test database name {$PMTEST_DB} or host {$PMTEST_HOST} is empty. It shouldn't ..."
	exit 1
fi

FIXTURE_FILE="$__DIR__/../tests/fixtures/datasets/SampleChannelsMediasAndSubscriptions.sql"

notice "importing fixture data into ${PMTEST_DB} (host : ${PMTEST_HOST})"
mysql -h${PMTEST_HOST} ${PMTESTDB_CREDS} ${PMTEST_DB} < $FIXTURE_FILE 
if [ "$?" != "0" ]; then
    echo "L importation des donnees de test dans la base de test a echoue !"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
