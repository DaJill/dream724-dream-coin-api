<?php
namespace App\Exceptions\CodeError\Bank;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\Bank\Payway;

class PaywayException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const path = Payway::class;

    /**
     * 代碼表.
     */
    const codeMap = [
        'PAYWAYS_TYPE_ERROR' => ['001', 'Payway is Not array', '金流管理格式錯誤'],
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
