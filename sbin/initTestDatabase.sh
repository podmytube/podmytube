#!/bin/bash
# This script will create a pmt test database to be used everywhere 
# without touching the original database :)
START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library


title "Init tests database"

if [ -z ${MYSQLSERVER_ROOT_PASSWORD} ]; then
	error "Credentials {${MYSQLSERVER_ROOT_PASSWORD}} for accessing mysqlServer container is empty. It shouldn't ..."
	exit 1
fi

SRC_DB="pmt"
DST_DB="pmtests"
CONNECTION_PARAMS="-hmysqlServer -uroot -p${MYSQLSERVER_ROOT_PASSWORD}"
DUMP_FILE="mydump.sql"

TABLES_TO_EXPORT_STRUCT_ONLY="channels users medias quotas thumbs scripts_duration subscriptions"
TABLES_TO_EXPORT_WITH_DATA="plans stripe_plans"
TABLES_TO_TRUNCATE="${TABLES_TO_EXPORT_STRUCT_ONLY} ${TABLES_TO_EXPORT_WITH_DATA}"

notice "creating $SRC_DB dump with those tables : $TABLES_TO_EXPORT_STRUCT_ONLY"
mysqldump $CONNECTION_PARAMS ${SRC_DB} --no-data $TABLES_TO_EXPORT_STRUCT_ONLY > $DUMP_FILE
if [ "$?" != "0" ]; then
    error "La creation du dump de $SRC_DB a echoue ! Inutile de continuer. "
    exit 1	
fi

notice "exporting tables $TABLES_TO_EXPORT_WITH_DATA -with data- from $SRC_DB dump"
mysqldump $CONNECTION_PARAMS ${SRC_DB} $TABLES_TO_EXPORT_WITH_DATA >> $DUMP_FILE
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
    mysql -hmysqlServer -uroot -p${MYSQLSERVER_ROOT_PASSWORD} -e "create database if not exists $DST_DB;grant all privileges on $DST_DB.* to 'podUserTests'@'localhost' identified by '@hFl0*pfUPzIsUJD';flush privileges;"
    if [ "$?" != "0" ]; then
        error "La creation de l'utilisateur et de la base ${DST_DB} a echoue !"
        exit 1	
    fi
else
    notice "Vidage (truncate) des tables $TABLES_TO_TRUNCATE de la base $DST_DB"
    mysql ${CONNECTION_PARAMS} ${DST_DB} -Nse 'show tables' | while read table; do mysql ${CONNECTION_PARAMS} ${DST_DB} -e "SET FOREIGN_KEY_CHECKS=0;truncate table $table"; done
    if [ "$?" != "0" ]; then
        error "Le truncate des tables de la base de tests a echouee !"
        exit 1	
    fi
    mysql ${CONNECTION_PARAMS} ${DST_DB} -e "SET FOREIGN_KEY_CHECKS=1"

fi


notice "Importation des données $DST_DB"
mysql ${CONNECTION_PARAMS} ${DST_DB} < $DUMP_FILE 
if [ "$?" != "0" ]; then
    echo "L importation des données de ${SRC_DB} dans la base ${DST_DB} a echoue !"
    exit 1	
fi

notice deleting dump file
rm -f $DUMP_FILE

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
