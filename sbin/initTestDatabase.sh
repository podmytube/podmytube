#!/bin/bash
# This script will create a pmt test database to be used everywhere 
# without touching the original database :)
START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library

title "Init tests database"

SRC_DB="pmt"
DUMP_FILE="mydump.sql"

# getting info from .env.testing file
ENV_FILE=".env.testing"
DST_DB_USER=$(read_var DB_USERNAME ${ENV_FILE})
DST_DB_PASS=$(read_var DB_PASSWORD ${ENV_FILE})
DST_DB_NAME=$(read_var DB_DATABASE ${ENV_FILE})
DST_DB_HOST=$(read_var DB_HOST ${ENV_FILE})

# cannot go through one --login-path, have to use unsecure way to pass 
# connections params
CONNECTION_PARAMS="-h${DST_DB_HOST} -u${DST_DB_USER} -p${DST_DB_PASS}"

TABLES_TO_EXPORT_STRUCT_ONLY="channels users medias quotas thumbs scripts_duration subscriptions channel_categories"
TABLES_TO_EXPORT_WITH_DATA="plans stripe_plans categories"
TABLES_TO_TRUNCATE="${TABLES_TO_EXPORT_STRUCT_ONLY} ${TABLES_TO_EXPORT_WITH_DATA}"

notice "creating $SRC_DB dump with those tables : $TABLES_TO_EXPORT_STRUCT_ONLY"
mysqldump ${CONNECTION_PARAMS} ${SRC_DB} --no-data $TABLES_TO_EXPORT_STRUCT_ONLY > $DUMP_FILE
if [ "$?" != "0" ]; then
    error "La creation du dump de $SRC_DB a echoue ! Inutile de continuer. "
    exit 1	
fi

notice "exporting tables $TABLES_TO_EXPORT_WITH_DATA -with data- from $SRC_DB dump"
mysqldump ${CONNECTION_PARAMS} ${SRC_DB} $TABLES_TO_EXPORT_WITH_DATA >> $DUMP_FILE
if [ "$?" != "0" ]; then
    echo "Le rajout des tables struct+data au dump de $SRC_DB a echoue ! Inutile de continuer."
    exit 1	
fi

# replacing "pmt" db name by podmytubeTests
sed -i "s/${SRC_DB}/${DST_DB_NAME}/g" $DUMP_FILE

notice "checking if ${DST_DB_NAME} exists"
mysqlshow ${CONNECTION_PARAMS} ${DST_DB_NAME} > /dev/null 2>&1
if [ "$?" != "0" ]; then
    notice "${DST_DB_NAME} does not exists - creating ${DST_DB_NAME}"
    mysql ${CONNECTION_PARAMS} -e "create database if not exists ${DST_DB_NAME};grant all privileges on ${DST_DB_NAME}.* to '${DST_DB_USER}'@'%' identified by '${DST_DB_PASS}';flush privileges;"
    if [ "$?" != "0" ]; then
        error "La creation de l'utilisateur et de la base ${DST_DB_NAME} a echoue !"
        exit 1	
    fi
else
    notice "Vidage (truncate) des tables $TABLES_TO_TRUNCATE de la base ${DST_DB_NAME}"
    mysql ${CONNECTION_PARAMS} ${DST_DB_NAME} -Nse 'show tables' | while read table; do mysql ${CONNECTION_PARAMS} ${DST_DB_NAME} -e "SET FOREIGN_KEY_CHECKS=0;truncate table $table"; done
    if [ "$?" != "0" ]; then
        error "Le truncate des tables de la base de tests a echouee !"
        exit 1	
    fi
    mysql ${CONNECTION_PARAMS} ${DST_DB_NAME} -e "SET FOREIGN_KEY_CHECKS=1"

fi


notice "Importation des données ${DST_DB_NAME}"
mysql ${CONNECTION_PARAMS} ${DST_DB_NAME} < $DUMP_FILE 
if [ "$?" != "0" ]; then
    echo "L importation des données de ${SRC_DB} dans la base ${DST_DB_NAME} a echoue !"
    exit 1	
fi

notice "deleting dump file"
rm -f $DUMP_FILE

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
