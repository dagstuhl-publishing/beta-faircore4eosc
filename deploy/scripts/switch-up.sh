#!/bin/bash
./artisan storage:link
./artisan config:cache
./artisan route:cache
./artisan view:cache
./artisan up
