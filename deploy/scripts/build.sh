#!/bin/bash
cp "/var/$DEPLOY_NAME/config/.env" .
rm -rf storage
ln -s "/var/$DEPLOY_NAME/storage"

npm install
npm run build

composer install --optimize-autoloader --no-dev

./artisan down
