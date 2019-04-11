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

# updating to add git, curl, mysql-client and sendmail
RUN     apt-get update -y && \
        apt-get install -y sendmail \
            libpng-dev \
            libjpeg-dev \
            wget \
            zip \
            git
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
RUN     chown -R www-data:www-data /srv/app
# Adding phpunit to Path
ENV     PATH="/srv/app/vendor/phpunit/phpunit:${PATH}"

WORKDIR /srv/app

# adding composer
COPY    .docker/installComposer.sh /var/opt/installComposer.sh
RUN     chmod +x /var/opt/installComposer.sh
RUN     /var/opt/installComposer.sh
RUN     composer install

# symlink for storage (the same that is created with "artisan storage:link")
RUN     if [ ! -L public/storage ]; then ln -s /srv/app/storage/app/public public/storage; fi


