FROM    php:7.3-apache-stretch

LABEL   maintainer="Frederick Tyteca"

# enabling mod rewrite
RUN     a2enmod rewrite

# setting timezone
ENV     TZ=Europe/Paris
RUN     ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# stuff to install
# libpng-dev --- used with laravel lib to resize/work with images
# libz-dev & libmemcached-dev are used for memcached
# libjpeg-dev --- used with laravel lib to resize/work with images
# wget --- used for the composer install
# zip --- is used by composer 
# git --- is used by composer 
# curl is used to download npm
RUN     apt-get update -y && \
        apt-get install -y -qq sendmail \
            libpng-dev \
            libz-dev libmemcached-dev \
            libjpeg-dev \
            curl \
            wget \
            zip \
            git \
            memcached

# installing nodejs
RUN     curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN     apt-get install -y nodejs
RUN     rm -rf /var/lib/apt/lists/*;

# installing memcached with pecl
RUN pecl install memcached

RUN docker-php-ext-configure gd \
        --with-png-dir=/usr/lib/ \
        --with-jpeg-dir=/usr/lib/ \
        --with-gd


# installing required php modules
# pdo & pdo_mysql for db connection
# gd ----- NOT SURE -----
# php-memcached for caching used for thumb for example
RUN     docker-php-ext-install pdo \
            pdo_mysql \
            gd && \
        docker-php-ext-enable memcached


# default vhost replaced by custom one
# mainly documentRoot /app/public
COPY    .docker/vhost.conf /etc/apache2/sites-available/000-default.conf


# copying all files (but .dockerignore(d)) in /app
COPY    . /app
WORKDIR /app
#RUN     chown -R www-data:www-data /app
RUN 	chown -R www-data:www-data storage bootstrap/cache
RUN 	chmod -R ug+rwx storage bootstrap/cache
RUN     chmod g+s storage/logs
# Adding phpunit to Path
ENV     PATH="/app/vendor/bin:${PATH}"


# compiling app.css and js
RUN     npm install && \
        npm run production

# adding composer (just in case)
COPY    .docker/installComposer.sh /var/opt/installComposer.sh
RUN     chmod +x /var/opt/installComposer.sh
RUN     /var/opt/installComposer.sh

# symlink for storage (the same that is created with "artisan storage:link")
RUN     if [ ! -L public/storage ]; then ln -s /app/storage/app/public public/storage; fi

RUN     chown -R www-data:www-data /app/storage


