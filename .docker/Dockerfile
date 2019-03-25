FROM    php:7.2.15-apache

LABEL   maintainer="Frederick Tyteca"

# setting apache user
ENV     APACHE_RUN_USER=www-data
ENV     APACHE_RUN_GROUP=www-data

# default vhost replaced by custom one
# mainly documentRoot /srv/app/public
COPY    .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# enabling mod rewrite
RUN     a2enmod rewrite

# setting timezone
ENV     TZ=Europe/Paris
RUN     ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# updating to add git, mysql-client and sendmail
RUN     apt-get update -y && \
        apt-get install -y sendmail \
            libpng-dev \
            git \
            mysql-client
RUN     rm -rf /var/lib/apt/lists/*;

# installing required php modules
RUN     docker-php-ext-install pdo \
            pdo_mysql \
            gd

# copying all files (but .dockerignore(d)) in /srv/app
COPY    . /srv/app
RUN     chown -R www-data:www-data /srv/app
# Adding phpunit to Path
ENV     PATH="/srv/app/vendor/phpunit/phpunit:${PATH}"

# getting dotfiles from github
RUN     cp /etc/skel/.bashrc ~/
RUN     git clone https://github.com/tyteck/dotfiles.git /root/dotfiles
RUN     ln -s dotfiles/.bash_aliases ~/.bash_aliases 

# creds to access db from mycl client within container
RUN     echo '#!/bin/bash\n\
export PMTDB_CREDS="-uroot -proot"\n\
export PMTESTDB_CREDS="-uroot -proot"'\
> /root/dotfiles/.creds

WORKDIR /srv/app

