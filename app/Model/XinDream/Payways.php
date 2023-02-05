<?php

namespace App\Model\XinDream;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payways extends Model
{
    protected $table = 'payways';
    protected $connection = 'XinDream';
    protected $fillable = [
        'id',
        'type',
        'code',
        'name',
        'upper_limit',
        'lower_limit',
        'updated_at',
        'amount',
        'active',
    ];

    public $primaryKey = ['token'];
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'id' => 'integer',
        'type' => 'integer',
        'code' => 'string',
        'name' => 'string',
        'upper_limit' => 'integer',
        'lower_limit' => 'integer',
        'updated_at' => 'datetime',
        'amount' => 'array',
        'active' => 'integer',
    ];

    /**
     * Undocumented function
     *
     * @param [Obj] $_oQuery
     * @param array $_aFields 更新項目
     * @param array $_iId 欲更新的資料id
     * @param [int] $_iUpperLimit 入款限額上限
     * @param [int] $_iLowerLimit 入款限額下線
     * @return void
     */
    public function scopeUpdateLimit($_oQuery, $_aFields=[], $_iId)
    {
        $aUpdate = [];
        foreach($_aFields as $iKey => $mVal) {
            if(!in_array($iKey, $this->fillable)) {
                continue;
            }
            if(is_array($mVal)) {
                $aUpdate[$iKey] = json_encode($mVal);
                continue;
            }
            $aUpdate[$iKey] = $mVal;
        }

        if(!empty($aUpdate)) {
            $oNow = Carbon::now();
            $sNow = $oNow->toDateTimeString();
            $aUpdate['updated_at'] = $sNow;
        }
        
        return $_oQuery
            ->where(['id'=>$_iId])
            ->update($aUpdate);
    }

}
