<?php
namespace App\Exceptions\CodeError\User;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\User\User;

class UserException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const path = User::class;

    /**
     * 代碼表.
     */
    const codeMap = [
        'ACCOUNT_EXIST' => ['001', 'create account error', '帳號已經存在'],
        'ACCOUNT_OR_PASSWORD_ERROR' => ['002', 'account or password error', '帳密錯誤'],
        'ACCOUNT_ACTIVE_TOKEN_ERROR' => ['003', 'account active error', '帳號激活驗證錯誤'],
        'ACCOUNT_ACTIVE_DEADLINE_ERROR' => ['004', 'account active error', '帳號激活時間超過'],
        'ACCOUNT_ACTIVE_ALREADY_ACTIVE_ERROR' => ['005', 'account active error', '帳號已經激活過了'],
        'ACCOUNT_SEARCH_DATE_TIME_ERROR' => ['006', 'account search error', '日期缺一不可'],
        'ACCOUNT_USUAL_ADDRESS_UPDATE_EMPTY' => ['007', 'account usual address update error', '常用住址更新資料空白'],
        'ACCOUNT_DATA_UPDATE_EMPTY' => ['008', 'account update error', '會員更新資料空白'],
        'ACCOUNT_ACTIVE_TOKEN_EMPTY' => ['009', 'account active error', '帳號激活驗證碼不存在'],
        'ACCOUNT_RESET_EMAIL_EMPTY' => ['010', 'account reset error', '重置密碼mail不存在'],
        'ACCOUNT_MAIL_EXIST' => ['011', 'create account error', '帳號信箱已經存在'],
        'ACCOUNT_NOT_ACTIVE_ERROR' => ['012', 'account or password error', '登入帳號未激活'],
        'ACCOUNT_RESEND_USER_NOT_EXIST' => ['013', 'resend email error', '信箱不存在或已激活或被停用'],
    ];

    public function __construct($errorKey, array $data = [])
    {
        $message = self::codeMap[$errorKey][1];

        if (!empty($data)) {
            $message .= '@'.json_encode($data);
        }
        parent::__construct($message, parent::mainCodeMap[__CLASS__].self::codeMap[$errorKey][0]);
    }
}
