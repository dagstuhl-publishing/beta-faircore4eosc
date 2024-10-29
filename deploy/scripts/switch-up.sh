#!/bin/bash
./artisan config:cache
./artisan route:cache
./artisan view:cache
./artisan up
