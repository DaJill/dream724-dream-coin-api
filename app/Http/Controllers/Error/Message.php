<?php

namespace App\Http\Controllers\Error;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CodeError\CodeErrorException;
use App\Exceptions\CodeError\Error\MessageException;

class Message extends Controller
{
    public function getErrorPathById($_iCode)
    {
        $oCodeErrorException = new CodeErrorException();
        $aPath = $oCodeErrorException->getCodeMessage($_iCode);
        if(empty($aPath))
        {
            throw new MessageException('CODE_NOT_EXIST');
        }
        
        return response()->json(['result' => true, 'data' => $aPath]);
    }
}