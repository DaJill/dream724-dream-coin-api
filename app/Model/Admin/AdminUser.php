<?php

namespace App\Model\Admin;

use DB;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'admin_user';
    protected $fillable = ['id', 'account', 'password', 'name', 'token', 'active', 'login_at', 'created_at', 'updated_at'];
    protected $hidden = ['password', 'token'];

    public $timestamps = false;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 取得會員List
     *
     * @param [type] $_oQuery
     * @param array $_aFields 顯示欄位
     * @param array $_aFieldsCondition 欄位條件(只能相等)
     * @return void
     */
    public function scopeGetList($_oQuery, $_aFields = [], $_aFieldsCondition=[])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        foreach($aFields as $iKey => $sVal) {
            if($sVal == 'password') { //密碼不能被撈
                unset($aFields[$iKey]);
            }
        }

        foreach($_aFieldsCondition as $sCol=>$oVal) {
            if(is_array($oVal)) {
                $_oQuery = $_oQuery->whereIn($sCol, $oVal);
                continue;
            }
            $_oQuery = $_oQuery->where($sCol, $oVal);
        }
        
        return $_oQuery
        ->get($aFields)
        ->toArray();
    }
}
