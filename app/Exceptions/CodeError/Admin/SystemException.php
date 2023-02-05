<?php
namespace App\Exceptions\CodeError\Admin;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\Admin\System;

class SystemException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const PATH = System::class;

    /**
     * 代碼表.
     */
    const CODE_MAP = [
        'INTERNAL_SERVER_ERROR'     => ['000', 'Internal server error', '內部伺服器錯誤'],
        'ACCOUNT_OR_PASSWORD_ERROR' => ['001', 'account or password error', '帳密錯誤'],
        'AUTHENTICATION_FAILED'     => ['002', 'Authentication failed', '登入失敗'],
        'PARAMETER_INCORRECT'       => ['003', 'The parameter is incorrect', '傳遞參數有誤'],
        'OLD_PASSWORD_INCORRECT'    => ['004', 'The old password is incorrect', '原密碼有誤'],

    ];

    public function __construct($errorKey, array $data = [])
    {
        $message = self::CODE_MAP[$errorKey][1];

        if (!empty($data)) {
            $message .= '@'.json_encode($data);
        }
        parent::__construct($message, parent::mainCodeMap[__CLASS__].self::CODE_MAP[$errorKey][0]);
    }
}
