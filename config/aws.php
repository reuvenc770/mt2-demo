<?php

return [
    'region' => env('AWS_REGION', ''),
    'version' => env('AWS_VERSION', ''),
    's3' => [
        'fileUploadBucket' => env('AWS_S3_UPLOAD_BUCKET', ''),
        'key' => env('AWS_S3_KEY', ''),
        'secret' => env('AWS_S3_SECRET', '')
    ],

];