<?php

namespace App\Model\Bank;

use Illuminate\Database\Eloquent\Model;

class Banks extends Model
{
    protected $table = 'banks';
    protected $connection = 'User';

    protected $casts    = ['code' => 'string', 'name' => 'string'];

    /**
     * 取得銀行資訊 - 下拉選單
     *
     * @return object
     */
    public static function getBanks()
    {
        return self::select('id','code','name')->orderBy('id','ASC')->get();
    }

    /**
     * 取得銀行資訊 - 下拉選單 by key
     *
     * @return object
     */
    public static function getBanksCodeIndex()
    {
        return self::select('code','name')->get()->keyBy('code')->toArray();
    }

}
