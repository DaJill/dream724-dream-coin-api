<?php
namespace App\Exceptions\CodeError\Bank;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\Bank\Bank;

class BankException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const path = Bank::class;

    /**
     * 代碼表.
     */
    const codeMap = [
        'DEPOSIT_ACCOUNT_IS_EXIST'           => ['001', 'deposit account is exist', '銀行帳號已經存在'],
        'DEPOSIT_ACCOUNT_IS_STATE_NOT_ALLOW' => ['002', 'deposit account is state not allow', '帳號狀態開啟時禁止刪除'],
        'DEPOSIT_ACCOUNT_IS_DEBIT_NOT_ZERO'  => ['003', 'deposit account is debit not zero', '審核中存點不等於0禁止刪除'],
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
