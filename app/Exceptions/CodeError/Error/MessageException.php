<?php
namespace App\Exceptions\CodeError\Error;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\Error\Message;

class MessageException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const path = User::class;

    /**
     * 代碼表.
     */
    const codeMap = [
        'CODE_NOT_EXIST' => ['001', 'code is not exist', 'error code不存在'],
    ];

    public function __construct($errorKey, array $data = [])
    {
        $message = self::codeMap[$errorKey][1];

        if (!empty($data)) {
            $message .= '@'.json_encode($data);
        }
        parent::__construct($message, parent::mainCodeMap[__CLASS__].self::codeMap[$errorKey][0]);
    }

    /**
     * 取得驗證資料和訊息.
     */
    public static function getCodeMap()
    {
        return self::codeMap;
    }
}
