FROM    php:7.2-apache

LABEL   maintainer="Frederick Tyteca"

# setting apache user
ENV     APACHE_RUN_USER=www-data
ENV     APACHE_RUN_GROUP=www-data

# enabling mod rewrite
RUN     a2enmod rewrite

# setting timezone
ENV     TZ=Europe/Paris
RUN     ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# stuff to install
# libpng-dev --- used with laravel lib to resize/work with images
# libjpeg-dev --- used with laravel lib to resize/work with images
# wget --- used for the composer install
# zip --- is used by composer 
# git --- is used by composer 
# mysql-client --- is used to get mysqldump (used for creating pmtests)
# curl is used to download npm
RUN     apt-get update -y && \
        apt-get install -y -qq sendmail \
            libpng-dev \
            libjpeg-dev \
            curl \
            wget \
            zip \
            git \
            mysql-client

# installing nodejs
RUN     curl -sL https://deb.nodesource.com/setup_10.x | bash -
RUN     apt-get install -y nodejs
RUN     rm -rf /var/lib/apt/lists/*;

RUN docker-php-ext-configure gd \
        --with-png-dir=/usr/lib/ \
        --with-jpeg-dir=/usr/lib/ \
        --with-gd

# installing required php modules
RUN     docker-php-ext-install pdo \
            pdo_mysql \
            gd

# default vhost replaced by custom one
# mainly documentRoot /srv/app/public
COPY    .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# copying all files (but .dockerignore(d)) in /srv/app
COPY    . /srv/app
WORKDIR /srv/app
#RUN     chown -R www-data:www-data /srv/app
RUN 	chown -R www-data:www-data storage bootstrap/cache
RUN 	chmod -R ug+rwx storage bootstrap/cache
# Adding phpunit to Path
ENV     PATH="/srv/app/vendor/bin:${PATH}"


# compiling app.css and js
RUN     npm install && npm update
RUN     npm run production

# adding composer (just in case)
COPY    .docker/installComposer.sh /var/opt/installComposer.sh
RUN     chmod +x /var/opt/installComposer.sh
RUN     /var/opt/installComposer.sh

# adding ll
RUN     sed -i "s/# alias ll='ls \$LS_OPTIONS -l'/alias ll='ls -lsa'/" /root/.bashrc

# symlink for storage (the same that is created with "artisan storage:link")
RUN     if [ ! -L public/storage ]; then ln -s /srv/app/storage/app/public public/storage; fi

RUN     chown -R www-data:www-data /srv/app/storage


