#!/bin/bash
# This script will do what is needed to run tests
echo "YOU SHOULD NOT DOING THIS !!!"

START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library

title "Importing tests fixtures"

# dumping source and creating tests database 
./sbin/initTestDatabase.sh
if [ "$?" != "0" ]; then
    error "Initializing test database has failed !"
    exit 1	
fi

# import testing data
./sbin/importTestFixtures.sh
if [ "$?" != "0" ]; then
    error "Test data importation into has failed !"
    exit 1	
fi


# migrating from channel_premium to subs
title "Seeding subscriptions for fixtures channels"
echo "seeding subscriptions"
php artisan db:seed --class=subscriptionTableSeeder --env=testing
echo "seeding categories"
php artisan db:seed --class=categoriesTableSeeder --env=testing

# import small errors to be checked for
./sbin/createErrorsToBeTested.sh
if [ "$?" != "0" ]; then
    error "Creation of errors to be tested has failed"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"
success "Tests DB is ready to use."