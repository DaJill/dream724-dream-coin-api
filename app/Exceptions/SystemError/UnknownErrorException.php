<?php

namespace App\Exceptions\SystemError;

class UnknownErrorException extends SystemErrorException
{
    const CodeMap = [
        'UnknowError' => '001',// system unknow error
        'NotFoundError' => '002',// url not found
    ];

    public static function getCodeMap()
    {
        return self::CodeMap;
    }
}