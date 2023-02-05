<?php

namespace App\Exceptions\CodeError\Middleware;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Middleware\MemApiMiddleware;

class MemMiddlewareException extends CodeErrorException
{
    /**
     * 程式路徑
     */
    const path = MemApiMiddleware::class;

    /**
     * 代碼表
     */
    const codeMap = [
        'AUTH_ERROR'  => ['001', 'authenticate key error', '認證錯誤'],
    ];

    public function __construct($error_key, array $data = [])
    {
        $message = self::codeMap[$error_key][1];

        if (!empty($data)) {
            $message .= '@' . json_encode($data);
        }

        parent::__construct($message, parent::mainCodeMap[__class__].self::codeMap[$error_key][0]);
    }
}
