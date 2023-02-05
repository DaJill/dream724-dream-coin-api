<?php
namespace App\Exceptions\CodeError\Admin;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\Admin\AdminUser;

class AdminUserException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const PATH = AdminUser::class;

    /**
     * 代碼表.
     */
    const CODE_MAP = [
        'INTERNAL_SERVER_ERROR' => ['000', 'Internal server error', '內部伺服器錯誤'],
        'PARAMETER_INCORRECT'   => ['001', 'The parameter is incorrect', '傳遞參數有誤'],
        'ACCOUNT_DUPLICATE'     => ['002', 'Has the account been used cannot duplicate registration', '帳號已存在無法重複新增'],
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
