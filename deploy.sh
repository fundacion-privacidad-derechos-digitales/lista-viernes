#!/usr/bin/env bash

composer dump-env prod
composer install --no-dev --optimize-autoloader
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
APP_ENV=prod php bin/console doctrine:schema:update --force
npm install
npm run build
chown -R listaviernes:clistaviernes ./*