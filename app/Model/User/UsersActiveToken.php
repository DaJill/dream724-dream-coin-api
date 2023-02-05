<?php

namespace App\Model\User;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UsersActiveToken extends Model
{
    protected $table = 'users_active_token';
    protected $connection = 'User';
    protected $fillable = [
        'id',
        'token',
        'token_active',
        'create_datetime',
        'deadline',
    ];
    
    public $primaryKey = ['token'];
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'id' => 'integer',
        'token' => 'string',
        'token_active' => 'integer',
        'create_datetime' => 'datetime',
        'deadline' => 'datetime',
    ];

    public function scopeAdd($_oQuery, $_iUserId, $_sToken, $_iDeadlineDays = 1 )
    {
        $dtNow = Carbon::now()->toDateTimeString();
        $dtDeadLine = Carbon::now()->modify('+'.$_iDeadlineDays.' days')->toDateTimeString();
        return self::insert(
            [
                'id' => $_iUserId,
                'token' => $_sToken,
                'token_active' => 0,
                'create_datetime' => $dtNow,
                'deadline' => $dtDeadLine,
            ]
        );
    }

    public function scopeUpdateToken($_oQuery, $_aFieldsUpdate, $_aFieldsCondition = [])
    {
        foreach($_aFieldsCondition as $sCol=>$oVal) {
            if(is_array($oVal)) {
                $_oQuery = $_oQuery->whereIn($sCol, $oVal);
                continue;
            }
            $_oQuery = $_oQuery->where($sCol, $oVal);
        }

        $aUpdate = [];
        foreach($_aFieldsUpdate as $sCol=>$oVal) {
            if(is_array($oVal)) {
                $oVal = json_encode($oVal);
            }
            $aUpdate[$sCol] = $oVal;
        }

        return $_oQuery->update($aUpdate);
    }
    public function scopeGetByToken($_oQuery, $_sToken, $_aFields = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        $aData = $_oQuery
        ->where('token', $_sToken)
        ->get($aFields)
        ->toArray();

        if(empty($aData)) {
            return $aData;
        }
        return $aData[0];
    }
    
}
