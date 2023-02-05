<?php

namespace App\Exceptions\CodeError\Middleware;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Middleware\CustomValidatorMiddleware;

class CustomValidatorMiddlewareException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const path = CustomValidatorMiddleware::class;

    public function __construct($errorKey, $category)
    {
        // 代碼表
        $sCode = config('param_validator.'.$category.'.code');
        $aCodeMap = $this->codeMap = config('param_validator.'.$category.'.exception');
        parent::__construct($aCodeMap[$errorKey][1], parent::mainCodeMap[__CLASS__].$sCode.$aCodeMap[$errorKey][0]);
    }
}
