#!/bin/bash
# this script will only import sample tests data

START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

DST_DB="podmytubeTests"
FIXTURE_FILE="$__DIR__/../tests/fixtures/datasets/SampleChannelsMediasAndSubscriptions.sql"

echo "importing fixture data into $DST_DB"
mysql --login-path=root $DST_DB < $FIXTURE_FILE 
if [ "$?" != "0" ]; then
    echo "L importation des donnees de test dans la base de test a echoue !"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
echo "script duration : ${ELAPSED_TIME}sec"
