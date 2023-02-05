<?php

namespace App\Model\User;

use Illuminate\Database\Eloquent\Model;

class UserUsualAddress extends Model
{
    protected $table = 'user_usual_address';
    protected $connection = 'User';
    protected $fillable = [
        'id',
        'account_id',
        'address',
        'convenience_store',
        'name',
        'mobile',
    ];
    public $primaryKey = ['id'];
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'id' => 'integer',
        'account_id' => 'integer',
        'address' => 'string',
        'convenience_store' => 'string',
        'name' => 'string',
        'mobile' => 'string',
    ];

    public function scopeAdd($_oQuery, $_iUserId, $_sAddress, $_sConvenienceStore, $_sName, $_sMobile)
    {
        
        return self::insert(
            [
                'account_id' => $_iUserId,
                'address' => $_sAddress,
                'convenience_store' => $_sConvenienceStore,
                'name' => $_sName,
                'mobile' => $_sMobile,
            ]
        );
    }

    public function scopeModifyById($_oQuery, $_iId, $_aFieldsCondition)
    {
        $aFieldsCondition = [];
        foreach($this->fillable as $iParamKey) {
            if(isset($_aFieldsCondition[$iParamKey])) {
                $aFieldsCondition[$iParamKey] = $_aFieldsCondition[$iParamKey];
            }
        }
        return $_oQuery
        ->where('id', $_iId)
        ->update($aFieldsCondition);
    }

    public function scopeGetByAccountId($_oQuery, $_iAccountId, $_aFields = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        return $_oQuery
        ->where('account_id', $_iAccountId)
        ->get($aFields)
        ->toArray();
    }

    public function scopeDelectById($_oQuery, $_iId)
    {
        return $_oQuery
        ->where('id', $_iId)
        ->delete();
    }
}
