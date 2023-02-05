<?php

return [
    // 呼叫api時帶的認證鑰
    'AuthenticateKey' => [
        'Mem'    => env('MEM_AUTH_KEY', ''),
        'Admin'  => env('ADMIN_AUTH_KEY', ''),
        'MemXin' => env('MEM_XIN_AUTH_KEY', ''),
    ],
    'url'             => [
        'platform'     => env('PLATFORM_URL'),
        'platform_api' => env('PLATFORM_API_URL'),
        'dream'        => env('DREAM_URL'),
        'pay'          => env('PAY_URL'),
        'xin_api'      => env('XIN_URL'),
    ],
];
