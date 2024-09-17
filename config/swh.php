<?php

return [

    "deposit_api" => [
        "url" => env("SWH_DEPOSIT_API_URL"),
        "username" => env("SWH_DEPOSIT_API_USERNAME"),
        "password" => env("SWH_DEPOSIT_API_PASSWORD"),
        "collection_name" => env("SWH_DEPOSIT_API_COLLECTION_NAME"),
    ],

    'web-api' => [
        'token' => env('SWH_WEB_API_TOKEN'),
        'url' => env('SWH_WEB_API_URL'),
        'cache-folder' => env('SWH_WEB_API_CACHE_FOLDER'), // absolute path to cache folder
        'cache-ttl' => env('SWH_WEB_API_CACHE_TTL'),
    ],

];
