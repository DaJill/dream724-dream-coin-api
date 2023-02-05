<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Controller;
use App\Model\Bank\Banks;

class Bank extends Controller
{
    /**
     * 取得銀行資訊 - 下拉選單
     *
     * @return object
     */

    public function getBank()
    {

        $aBanks = Banks::getBanks();

        foreach ($aBanks as $key => $value){
            $value['name'] = $value['code'].'-'.$value['name'];
        }

        return response()->json(['result' => true, 'data' => $aBanks]);
    }

    /**
     * 取得1177銀行資訊 - 下拉選單
     *
     * @return object
     */

    public function getBank1177()
    {

        $aBanks = [
            ['id' => 1,'code' => '007' ,'name' => '第一銀行'],
            ['id' => 2,'code' => '009' ,'name' => '彰化銀行'],
            ['id' => 3,'code' => '013' ,'name' => '國泰世華'],
            ['id' => 4,'code' => '807' ,'name' => '永豐銀行'],
            ['id' => 5,'code' => '812' ,'name' => '台新銀行']
        ];

        foreach ($aBanks as $key => $value){
            $value['name'] = $value['code'].'-'.$value['name'];
        }

        return response()->json(['result' => true, 'data' => $aBanks]);
    }
}
