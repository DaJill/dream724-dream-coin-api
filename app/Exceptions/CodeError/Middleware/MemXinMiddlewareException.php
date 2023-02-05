<?php

namespace App\Exceptions\CodeError\Middleware;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Middleware\MemXinApiMiddleware;

class MemXinMiddlewareException extends CodeErrorException
{
    /**
     * 程式路徑
     */
    const path = MemXinApiMiddleware::class;

    /**
     * 代碼表
     */
    const codeMap = [
        'AUTH_ERROR'  => ['001', 'authenticate key error', '認證錯誤'],
        'IP_NOT_IN_WHITE_LIST'  => ['002', 'no permission', 'IP不在白名單內'],
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
