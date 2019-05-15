#!/bin/bash
# create/update the documentation of the dashboard project
START_TIME=$SECONDS

# script __DIR__ location
__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# bash text color and formatting
source $__DIR__/.bash_library

title "phpdoc"

# getting info from .env.testing file
ENV_FILE=".env"
APP_ENV=$(read_var APP_ENV ${ENV_FILE})
if [ "$APP_ENV" = "prod" ];then
    warning "this documentation should not been published on production environment"
fi

WORKING_PATH="$__DIR__/../"
DOCUMENTATION_PATH="$__DIR__/../documentation/"

docker run --rm --user $(id -u):$(id -g) -v $WORKING_PATH:/data phpdoc/phpdoc
if [ $? != 0 ];then
    error "something went wrong during documentation process"
fi

ELAPSED_TIME=$(($SECONDS - $START_TIME))
notice "script duration : ${ELAPSED_TIME}sec"