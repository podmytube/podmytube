#
# composer
#
FROM composer:2.0 as composer

COPY database database
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#
# node
#
FROM node:14.15-buster as node

RUN mkdir -p /app/public

COPY package.json webpack.mix.js tailwind.config.js postcss.config.js /app/
COPY resources/images/ /app/resources/images/
COPY resources/js/ /app/resources/js/
COPY resources/sass/ /app/resources/sass/

WORKDIR /app

RUN npm install && npm run production


#
# App
#
FROM    php:8.1.8-apache-buster

ARG USER_ID
ARG GROUP_ID

# enabling mod rewrite
RUN     a2enmod rewrite

# setting timezone
ENV     TZ=Europe/Paris
RUN     ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# stuff to install
# libpng-dev --- used with laravel lib to resize/work with images
# libjpeg-dev --- used with laravel lib to resize/work with images
# locales is used with youtube-dl to avoid naming problem
# ffmpeg is used to convert video to audio by youtube-dl
# supervisor is used to manage backgroud processes and queues
# sqlite is for tests
# ssh is for connecting on remote with shell_exec commands
# rsync to transfer files
# python is useful to run youtube-dl
RUN apt-get update -y && \
    apt-get install -y -qq libpng-dev \
    libjpeg-dev \
    locales \
    ffmpeg \
    supervisor \
    sqlite \
    python3 \
    ssh \
    rsync && \
    rm -rf /var/lib/apt/lists/*;

# Set the locale
RUN sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen && locale-gen
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

# installing required php modules
# pdo & pdo_mysql for db connection
# gd ----- NOT SURE -----
RUN     docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install pdo_mysql gd

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
    userdel -f www-data &&\
    if getent group www-data ; then groupdel www-data; fi &&\
    groupadd -g ${GROUP_ID} www-data &&\
    useradd -l -u ${USER_ID} -g www-data www-data &&\
    install -d -m 0755 -o www-data -g www-data /home/www-data &&\
    chown --changes --silent --no-dereference --recursive \
    --from=33:33 ${USER_ID}:${GROUP_ID} \
    /home/www-data \
    ;fi

# default vhost replaced by custom one                                                                                                                                                                                          
# mainly documentRoot /app/public       
COPY    .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# disabling php short tags (IE : <? ?>)
COPY    .docker/additionnalConf.ini /usr/local/etc/php/conf.d/

COPY --from=composer /app/vendor/ /app/vendor/
COPY --from=node /app/public/js/ /app/public/js/
COPY --from=node /app/public/css/ /app/public/css/
COPY --from=node /app/mix-manifest.json /app/mix-manifest.json
COPY . /app
WORKDIR /app

USER www-data

# symlink for storage (the same that is created with "artisan storage:link")
RUN     if [ ! -L public/storage ]; then ln -s /app/storage/app/public public/storage; fi

ENTRYPOINT [ "apache2-foreground" ]