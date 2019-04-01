#!/bin/bash
# This script will do what is needed to run tests

START_TIME=$SECONDS

if [ -f ~/dotfiles/.bash_functions ]; then
	source ~/dotfiles/.bash_functions
fi

title "Importing tests fixtures"

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

./sbin/initTestDatabase.sh
if [ "$?" != "0" ]; then
    error "Initializing test database has failed !"
    exit 1	
fi

./sbin/importTestFixtures.sh
if [ "$?" != "0" ]; then
    error "Test data importation into has failed !"
    exit 1	
fi

TABLES2SEED="subscriptionTableSeeder usersTableSeeder"
title "Seeding ..."
for TABLE2SEED in ${TABLES2SEED}
do
    notice "Seeding $TABLE2SEED."
    php artisan db:seed --env=testing --class=$TABLE2SEED
    if [ "$?" != "0" ]; then
        error "Seeding $TABLE2SEED has failed"
        exit 1	
    fi    
done

./sbin/createErrorsToBeTested.sh
if [ "$?" != "0" ]; then
    error "Creation of errors to be tested has failed"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
success "Tests DB is ready to use."