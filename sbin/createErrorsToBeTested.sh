#!/bin/bash
# this script will only create some errors to be tested

START_TIME=$SECONDS

DST_DB="podmytubeTests"

echo "Removing subscription for invalidChannel"
mysql --login-path=root $DST_DB -e "delete from subscriptions where channel_id='invalidChannel'"
if [ "$?" != "0" ]; then
    echo "La suppression de la subscription de la chaine invalidChannel dans la base de test a echoue !"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
echo "script duration : ${ELAPSED_TIME}sec"
