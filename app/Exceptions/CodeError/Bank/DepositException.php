<?php
namespace App\Exceptions\CodeError\Bank;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\Bank\Deposit;

class DepositException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const path = Deposit::class;

    /**
     * 代碼表.
     */
    const codeMap = [
        'DEPOSIT_DROPDOWN_IS_ERROR'  => ['001', 'deposit dropdown is error', '沒有此下拉選單'],
        'DEPOSIT_DATE_IS_NOT_NULL'   => ['002', 'deposit date is not null', '日期不可為空'],
        'DEPOSIT_STATE_IS_ERROR'     => ['003', 'deposit state is error', '訂單非審核中狀態'],
        'DEPOSIT_UPDATE_IS_ERROR'    => ['004', 'deposit update is error', '訂單更新失敗'],
        'DEPOSIT_REASON_IS_NOT_NULL' => ['005', 'deposit reason is not null', '原因不可為空'],
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
