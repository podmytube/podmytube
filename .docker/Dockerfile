FROM php:7.2.15-apache

LABEL maintainer="Frederick Tyteca"

# setting apache user
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data

# setting timezone
ENV TZ=Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update -y && \
    apt-get install -y sendmail libpng-dev

# copying all files (but .dockerignore(d)) in /srv/app
COPY . /srv/app
RUN chown -R www-data:www-data /srv/app

# default vhost replaced vy custome one
# mainly documentRoot /srv/app/public
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /srv/app

# enabling mod rewrite
RUN a2enmod rewrite

# installing requirer php modules
RUN docker-php-ext-install mbstring && \
    docker-php-ext-install pdo && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install gd