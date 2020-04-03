#!/bin/bash

cd /var/www/fw_melhoremcasa

#git pull origin marcelo
git pull

#composer update --optimize-autoloader --no-dev

#php artisan config:cache

#php artisan route:cache

cp -R /var/www/fw_melhoremcasa/public /var/www/html/melhoremcasa/
cp -R /var/www/fw_melhoremcasa/public/css /var/www/html/melhoremcasa/
cp -R /var/www/fw_melhoremcasa/public/fonts /var/www/html/melhoremcasa/
cp -R /var/www/fw_melhoremcasa/public/img /var/www/html/melhoremcasa/
cp -R /var/www/fw_melhoremcasa/public/js /var/www/html/melhoremcasa/

#/etc/init.d/apache2 restart

