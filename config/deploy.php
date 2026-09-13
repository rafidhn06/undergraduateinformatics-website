<?php

return [
    'token' => env('DEPLOY_TOKEN', ''),
    'directory' => env('DEPLOY_DIR', dirname(base_path()) . '/deploy'),
    'app_path' => env('DEPLOY_APP_PATH', base_path()),
    'public_path' => env('DEPLOY_PUBLIC_PATH', public_path()),
];
