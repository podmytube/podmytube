#!/bin/bash
# This script will create a pmt test database to be used everywhere 
# without touching the original database :)
START_TIME=$SECONDS

# colors
red="\[\033[38;5;196m\]"
green="\[\033[38;5;10m\]"

# font weight
normal="\[$(tput sgr0)\]"

if [ -z ${PMT_DB} ]; then
	echo "PMT database name is not in the environment variable. It should ..."
	exit 1
fi

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

SRC_DB=${PMT_DB}
DST_DB=${PMT_TEST_DB}
DUMP_FILE="mydump.sql"

TABLES_TO_EXPORT_STRUCT_ONLY="channels users medias quotas thumbs scripts_duration subscriptions"
TABLES_TO_EXPORT_WITH_DATA="plans stripe_plans"
TABLES_TO_TRUNCATE="${TABLES_TO_EXPORT_STRUCT_ONLY} ${TABLES_TO_EXPORT_WITH_DATA}"

echo "creating $SRC_DB dump with those tables : $TABLES_TO_EXPORT_STRUCT_ONLY"
mysqldump  --login-path=root --no-data $SRC_DB $TABLES_TO_EXPORT_STRUCT_ONLY > $DUMP_FILE
if [ "$?" != "0" ]; then
    echo "La creation du dump de $SRC_DB a echoue ! Inutile de continuer."
    exit 1	
fi

echo "exporting tables $TABLES_TO_EXPORT_WITH_DATA (with data) from $SRC_DB dump"
mysqldump  --login-path=root $SRC_DB $TABLES_TO_EXPORT_WITH_DATA >> $DUMP_FILE
if [ "$?" != "0" ]; then
    echo "Le rajout des tables struct+data au dump de $SRC_DB a echoue ! Inutile de continuer."
    exit 1	
fi

# replacing "pmt" db name by podmytubeTests
sed -i 's/$SRC_DB/$DST_DB/g' $DUMP_FILE

echo "checking if $DST_DB exists"
`mysqlshow --login-path=root $DST_DB > /dev/null 2>&1`
if [ "$?" != "0" ]; then
    echo "$DST_DB does not exists - creating $DST_DB"
    mysql --login-path=root -e "create database if not exists $DST_DB;grant all privileges on $DST_DB.* to 'podUserTests'@'localhost' identified by '@hFl0*pfUPzIsUJD';flush privileges;"
    if [ "$?" != "0" ]; then
        echo "La creation de l'utilisateur et de la base ${DST_DB} a echoue !"
        exit 1	
    fi
else
    echo "emptying $TABLES_TO_TRUNCATE"
    mysql --login-path=root -Nse 'show tables' $DST_DB | while read table; do mysql --login-path=root -e "SET FOREIGN_KEY_CHECKS=0;truncate table $table" $DST_DB; done
    mysql --login-path=root -e "SET FOREIGN_KEY_CHECKS=1"

fi

echo "importing into $DST_DB"
mysql --login-path=root $DST_DB < $DUMP_FILE 
if [ "$?" != "0" ]; then
    echo "L importation de la structure de ${SRC_DB} dans la base ${DST_DB} a echoue !"
    exit 1	
fi

echo deleting dump file
rm -fv $DUMP_FILE

ELAPSED_TIME=$(($SECONDS - $START_TIME))
echo "script duration : ${ELAPSED_TIME}sec"
