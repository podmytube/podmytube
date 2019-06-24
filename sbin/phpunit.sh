#!/bin/bash
# This script is running tests

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library

title "=========================================="
notice "Running config test"
docker exec -it dash phpunit --colors --no-configuration tests/Unit/GetConfTest.php

notice "Running global tests"
docker exec -it dash phpunit
