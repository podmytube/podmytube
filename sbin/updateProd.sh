#!/bin/bash

PROD_PATH=/home/www/dashboard.podmytube.com
cd $PROD_PATH

# updating code
git pull

# creating symlink from public to storage
if [[ ! -L "$PROD_PATH/public/storage" ]];then
	php artisan storage:link
fi

# installing php modules
composer install --no-dev

# building css/js
npm install && npm run production

# updating database migrations
php artisan migrate

# return back
cd -
