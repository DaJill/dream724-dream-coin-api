<?php

namespace App\Model\Bank;

use Illuminate\Database\Eloquent\Model;

class Payways extends Model
{
    protected $table = 'payways';
    protected $connection = 'User';

    protected $fillable = ['upper_limit', 'lower_limit', 'amount', 'active'];
    protected $casts    = ['amount' => 'array', 'upper_limit' => 'integer', 'lower_limit' => 'integer'];

    /**
     * 取得金流管理列表
     *
     * @return object
     */
    public static function getPaywaysList()
    {
        return self::select('type','name','upper_limit','lower_limit','amount','active')->orderBy('id','ASC')->get();
    }

    /**
     * 取得金流管理列表 - type為key
     *
     * @return object
     */
    public static function getPaywaysListByType()
    {
        $result = self::select('id','type','name','upper_limit','lower_limit','amount','active')->orderBy('id','ASC')->get();
        return $result->keyBy('type');
    }

    /**
     * 修改金流管理列表
     *
     * @param  integer $id
     * @param  array   $parameters
     *
     * @return object
     */
    public static function updatePayways($id, array $parameters = [])
    {
        return self::find($id)->update($parameters);
    }
}
