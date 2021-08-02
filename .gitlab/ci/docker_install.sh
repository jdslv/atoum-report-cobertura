#! /bin/sh

set -xe

# We need to install dependencies only for Docker
if [ ! -e /.dockerenv ]; then
    exit 0
fi

apt update -yqq
apt install git zip unzip -yqq

# composer
EXPECTED_CHECKSUM="$(curl https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer composer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --filename=composer --install-dir=/usr/local/bin --quiet
rm composer-setup.php


# xdebug
pecl install xdebug
docker-php-ext-enable xdebug

echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
