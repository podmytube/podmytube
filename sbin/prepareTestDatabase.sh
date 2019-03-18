#!/bin/bash
# This script will do what is needed to run tests

START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

./sbin/initTestDatabase.sh
if [ "$?" != "0" ]; then
    echo "Initializing test database ${PMT_TEST_DB} has failed !"
    exit 1	
fi

echo "importing test fixtures"
./sbin/importTestFixtures.sh
if [ "$?" != "0" ]; then
    echo "Test data importation into ${PMT_TEST_DB} has failed !"
    exit 1	
fi

echo "seeding subscriptions"
php artisan db:seed --env=testing --class=subscriptionTableSeeder
if [ "$?" != "0" ]; then
    echo "Seeding subscriptions has failed"
    exit 1	
fi

echo "creating errors to be tested"
./sbin/createErrorsToBeTested.sh
if [ "$?" != "0" ]; then
    echo "Creation of errors to be tested has failed"
    exit 1	
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
echo "script duration : ${ELAPSED_TIME}sec"
