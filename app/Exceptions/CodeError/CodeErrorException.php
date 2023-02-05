<?php

namespace App\Exceptions\CodeError;

use RuntimeException;
use App\Exceptions\CodeError\Middleware\CustomValidatorMiddlewareException;
use App\Exceptions\CodeError\Middleware\MemMiddlewareException;
use App\Exceptions\CodeError\Middleware\MemXinMiddlewareException;
use App\Exceptions\CodeError\Middleware\AdminMiddlewareException;
use App\Exceptions\CodeError\User\UserException;
use App\Exceptions\CodeError\Product\ProductException;
use App\Exceptions\CodeError\Error\MessageException;
use App\Exceptions\CodeError\Admin\SystemException;
use App\Exceptions\CodeError\Bank\PaywayException;
use App\Exceptions\CodeError\Product\ProductOrderException;
use App\Exceptions\CodeError\Bank\BankException;
use App\Exceptions\CodeError\Admin\AdminUserException;
use App\Exceptions\CodeError\Bank\DepositException;

class CodeErrorException extends RuntimeException
{
    //主代碼表
    const mainCodeMap = [
        CustomValidatorMiddlewareException::class => '1001',
        UserException::class => '1002',
        MemMiddlewareException::class => '1003',
        MessageException::class => '1004',
        ProductException::class => '1005',
        AdminMiddlewareException::class => '1006',
        SystemException::class => '1007',
        PaywayException::class => '1008',
        ProductOrderException::class => '1009',
        BankException::class => '1010',
        AdminUserException::class => '1011',
        DepositException::class => '1012',
        MemXinMiddlewareException::class => '1013',
    ];

    public function render($request)
    {
        $massage = explode('@', $this->getMessage());

        if (!empty($massage[1])) {
            return response()->json(['result' => false, 'message' => $massage[0], 'code' => $this->getCode(), 'data' => json_decode($massage[1])], 200);
        } else {
            return response()->json(['result' => false, 'message' => $massage[0], 'code' => $this->getCode()], 200);
        }
    }

    /**
     * 取得ErrorCode的相關資訊
     *
     * @param [int] $_iCode ErrorCode
     * @return void
     */
    public function getCodeMessage($_iCode)
    {
        $sMainCode = substr($_iCode, 0, 4);
        $oExceptionClass = array_search($sMainCode, self::mainCodeMap);

        if ($oExceptionClass === false) {
            return [];
        }

        $aCodeMap = [];
        $sCode = '';
        $sValidatorCategory = '';

        //參數驗證的Exception
        if ($oExceptionClass === CustomValidatorMiddlewareException::class) {
            $sCode = substr($_iCode, 7);
            $sCategoryCode = substr($_iCode, 4, 3);
            $aParamError = config('param_validator');
            foreach ($aParamError as $sCategory => $aRowParamError) {
                if ($aRowParamError['code'] === $sCategoryCode) {
                    $sValidatorCategory = $sCategory;
                    $aCodeMap = $aRowParamError['exception'];
                }
            }
        } else { //Controller的Exception
            $sCode = substr($_iCode, 4);
            $aCodeMap = $oExceptionClass::codeMap;
        }

        if (array_search($sCode, array_column($aCodeMap, 0)) === false) {
            return [];
        }

        foreach ($aCodeMap as $aRowCodeMap) {
            if ($aRowCodeMap[0] === $sCode) {
                if ($sValidatorCategory !== '') {
                    return [
                        'exception_path'=> $oExceptionClass,
                        'http_path'=> $oExceptionClass::path,
                        'validator_category' => $sValidatorCategory,
                        'error_message' => $aRowCodeMap[1],
                        'message' => $aRowCodeMap[2],
                    ];
                }

                return [
                    'exception_path'=> $oExceptionClass,
                    'http_path' => $oExceptionClass::path,
                    'error_message' => $aRowCodeMap[1],
                    'message' => $aRowCodeMap[2],
                ];
            }
        }

        return [];
    }
}
