FROM    php:7.2.15-apache

LABEL   maintainer="Frederick Tyteca"

# setting apache user
ENV     APACHE_RUN_USER=www-data
ENV     APACHE_RUN_GROUP=www-data

# setting timezone
ENV     TZ=Europe/Paris
RUN     ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN     apt-get update -y && \
        apt-get install -y sendmail \
            libpng-dev \
            git 
RUN     rm -rf /var/lib/apt/lists/*;

# installing required php modules
RUN     docker-php-ext-install pdo \
            pdo_mysql \
            gd

# copying all files (but .dockerignore(d)) in /srv/app
COPY    . /srv/app
RUN     chown -R www-data:www-data /srv/app

# default vhost replaced by custom one
# mainly documentRoot /srv/app/public
COPY    .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# getting dotfiles from github
RUN     cp /etc/skel/.bashrc ~/
RUN     git clone https://github.com/tyteck/dotfiles.git /root/dotfiles
RUN     ln -s dotfiles/.bash_aliases ~/.bash_aliases && touch /root/dotfiles/.creds


WORKDIR /srv/app

# enabling mod rewrite
RUN     a2enmod rewrite