#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
cp .env .env
cp .env.testing .env.testing
chown -R www-data:www-data .
composer install
php artisan key:generate
php artisan migrate

php-fpm
