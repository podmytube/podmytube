#!/bin/bash
# This script will create a pmt test database to be used everywhere 
# without touching the original database :)
START_TIME=$SECONDS

if [ -f ~/dotfiles/.bash_functions ]; then
	source ~/dotfiles/.bash_functions
fi

title "Init tests database"

if [ -z ${PMT_DB} ] || [ -z ${PMT_HOST} ]; then
	error "Either pmt database name {$PMT_DB} or host {$PMT_HOST} is empty. It shouldn't ..."
	exit 1
fi

if [ -z ${PMTEST_DB} ] || [ -z ${PMTEST_HOST} ]; then
	error "Either test database name {$PMTEST_DB} or host {$PMTEST_HOST} is empty. It shouldn't ..."
	exit 1
fi

if [ -z "${PMTDB_CREDS}" ] || [ -z "${PMTESTDB_CREDS}" ]; then
	error "Either src PMTDB_CREDS {$PMTDB_CREDS} or dst {$PMTESTDB_CREDS} credentials is/are empty. It shouldn't ..."
	exit 1
fi

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

SRC_DB=${PMT_DB}
SRC_BASE_PARAMS="-h$PMT_HOST $PMTDB_CREDS $SRC_DB"
DST_DB=${PMTEST_DB}
DST_BASE_PARAMS="-h$PMTEST_HOST $PMTESTDB_CREDS $DST_DB"
DUMP_FILE="mydump.sql"

TABLES_TO_EXPORT_STRUCT_ONLY="channels users medias quotas thumbs scripts_duration subscriptions"
TABLES_TO_EXPORT_WITH_DATA="plans stripe_plans"
TABLES_TO_TRUNCATE="${TABLES_TO_EXPORT_STRUCT_ONLY} ${TABLES_TO_EXPORT_WITH_DATA}"

notice "creating $SRC_DB dump with those tables : $TABLES_TO_EXPORT_STRUCT_ONLY"
mysqldump $SRC_BASE_PARAMS --no-data $TABLES_TO_EXPORT_STRUCT_ONLY > $DUMP_FILE
if [ "$?" != "0" ]; then
    error "La creation du dump de $SRC_DB a echoue ! Inutile de continuer. "
    exit 1	
fi

notice "exporting tables $TABLES_TO_EXPORT_WITH_DATA -with data- from $SRC_DB dump"
mysqldump $SRC_BASE_PARAMS $TABLES_TO_EXPORT_WITH_DATA >> $DUMP_FILE
if [ "$?" != "0" ]; then
    echo "Le rajout des tables struct+data au dump de $SRC_DB a echoue ! Inutile de continuer."
    exit 1	
fi

# replacing "pmt" db name by podmytubeTests
sed -i 's/$SRC_DB/$DST_DB/g' $DUMP_FILE

notice "checking if $DST_DB exists"
mysqlshow $DST_BASE_PARAMS > /dev/null 2>&1
if [ "$?" != "0" ]; then
    notice "$DST_DB does not exists - creating $DST_DB"
    mysql -h$PMT_HOST $PMTDB_CREDS -e "create database if not exists $DST_DB;grant all privileges on $DST_DB.* to 'podUserTests'@'localhost' identified by '@hFl0*pfUPzIsUJD';flush privileges;"
    if [ "$?" != "0" ]; then
        error "La creation de l'utilisateur et de la base ${DST_DB} a echoue !"
        exit 1	
    fi
else
    notice "Vidage (truncate) des tables $TABLES_TO_TRUNCATE de la base $DST_DB"
    mysql $DST_BASE_PARAMS -Nse 'show tables' | while read table; do mysql $DST_BASE_PARAMS -e "SET FOREIGN_KEY_CHECKS=0;truncate table $table"; done
    if [ "$?" != "0" ]; then
        error "Le truncate des tables de la base de tests a echouee !"
        exit 1	
    fi
    mysql $DST_BASE_PARAMS -e "SET FOREIGN_KEY_CHECKS=1"

fi


notice "Importation des données $DST_DB"
mysql $DST_BASE_PARAMS < $DUMP_FILE 
if [ "$?" != "0" ]; then
    echo "L importation des données de ${SRC_DB} dans la base ${DST_DB} a echoue !"
    exit 1	
fi

notice deleting dump file
rm -f $DUMP_FILE

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
