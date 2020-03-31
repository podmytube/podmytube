FROM    php:7.4-apache-buster

LABEL   maintainer="Frederick Tyteca"

# used to create specific user with same uid/gid
ARG         USER_ID
ARG         GROUP_ID

# enabling mod rewrite
RUN     a2enmod rewrite

# setting timezone
ENV     TZ=Europe/Paris
RUN     ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# stuff to install
# libpng-dev --- used with laravel lib to resize/work with images
# libjpeg-dev --- used with laravel lib to resize/work with images
# libzip-dev --- used by composer
# zip --- used by composer
# git -- used by composer
# curl is used to download npm
RUN     apt-get update -y && \
        apt-get install -y -qq sendmail \
            libpng-dev \
            libjpeg-dev \
            supervisor \
            sqlite \
            libzip-dev \
            zip \
            git
            
RUN     rm -rf /var/lib/apt/lists/*;

RUN     docker-php-ext-install zip
RUN     docker-php-ext-configure gd --with-jpeg

# installing required php modules
# pdo & pdo_mysql for db connection
# gd ----- NOT SURE -----
RUN     docker-php-ext-install pdo \
            pdo_mysql \
            gd

# default vhost replaced by custom one
# mainly documentRoot /app/public
COPY    .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
# copying supervisor conf for queue worker
COPY    .docker/laravel-worker.conf /etc/supervisor/conf.d/

# disabling php short tags (IE : <? ?>)
COPY    .docker/disablingShortTags.ini /usr/local/etc/php/conf.d/

# creating appropriate user
RUN         addgroup --gid $GROUP_ID dockeruser
RUN         adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID dockeruser

# copying all files (but .dockerignore(d)) in /app
COPY    . /app
WORKDIR /app
RUN 	mkdir -p bootstrap/cache && \
        chown -R www-data:dockeruser storage bootstrap && \
        chmod -R ug+rw storage bootstrap && \
        chmod g+s storage/logs
# Adding phpunit to Path
ENV     PATH="/app/vendor/bin:${PATH}"

# symlink for storage (the same that is created with "artisan storage:link")
RUN     if [ ! -L public/storage ]; then ln -s /app/storage/app/public public/storage; fi

# this script will launch supervisor then apache on the foreground
COPY    .docker/run-on-start.sh /etc/init.d/run-on-start.sh
CMD     ["/etc/init.d/run-on-start.sh"]