<?php

namespace App\Model\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'users';
    protected $connection = 'User';
    protected $fillable = [
        'id',
        'hallid',
        'account',
        'nickname',
        'password',
        'mobile',
        'active',
        'email',
        'memo',
        'token_active',
        'create_datetime',
        'modify_datetime',
        'token',
    ];

    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'id' => 'integer',
        'hallid' => 'integer',
        'account' => 'string',
        'nickname' => 'string',
        'password' => 'string',
        'mobile' => 'string',
        'active' => 'integer',
        'email' => 'string',
        'memo' => 'string',
        'token_active' => 'integer',
        'create_datetime' => 'datetime',
        'modify_datetime' => 'datetime',
        'token' => 'string',
    ];

    protected $hidden = ['password', 'token'];

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
     * 新增帳號
     *
     * @param [int]    $hallid   廳主ID
     * @param [string] $account  帳號
     * @param [string] $password 密碼
     *
     * @return object
     */
    public function scopeAdd($_oQuery,
    $_iHallid,
    $_sAccount,
    $_sPassword,
    $_sMobile,
    $_sEmail,
    $_sNickname,
    $_sActive,
    $_sTokenActive
    )
    {
        $oNow = Carbon::now();
        $sNow = $oNow->toDateTimeString();
        return self::insertGetId(
            [
                'hallid' => $_iHallid,
                'account' => $_sAccount,
                'nickname' => $_sNickname,
                'password' => $_sPassword,
                'mobile' => $_sMobile,
                'email' => $_sEmail,
                'active' => $_sActive,
                'token_active' => $_sTokenActive,
                'create_datetime' => $sNow,
                'modify_datetime' => $sNow,
            ]
        );
    }

    /**
     * 更新密碼
     * TODO.
     *
     * @param [Object] $query    PDO
     * @param [int]    $hallid   廳主ID
     * @param [string] $account  帳號
     * @param [string] $password 密碼
     */
    public function scopeUpdatePassword($_oQuery, $_iHallid, $_sAccount, $_sPassword)
    {
        $oNow = Carbon::now();
        $sNow = $oNow->toDateTimeString();
        $aUpdate = [
            'password' => $_sPassword,
            'modify_datetime' => $sNow
        ];
        return $_oQuery
        ->where('hallid', $_iHallid)
        ->where('account', $_sAccount)
        ->update($aUpdate);
    }

    public function scopeGetPassword($_oQuery, $_iHallid, $_sAccount)
    {
        return $_oQuery
            ->where('hallid', $_iHallid)
            ->where('account', $_sAccount)
            ->get(['password'])
            ->pluck('password')[0];
    }

    /**
     * 用ID取得使用者資料
     *
     * @param [Object] $oQuery
     * @param [Int] $_iId 使用者ID
     * @param array $_aFields 欲取得的欄位
     * ['id', 'hallid', 'account', 'nickname', 'mobile', 'currency', 'active', 'create_time']
     * @return array 使用者資料
     */
    public function scopeGetUserById($_oQuery, $_iId, $_aFields = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        foreach($aFields as $iKey => $sVal) {
            if($sVal == 'password') { //密碼不能被撈
                unset($aFields[$iKey]);
            }
        }

        $aData = $_oQuery
            ->where('id', $_iId)
            ->get($aFields)
            ->toArray();

        if(empty($aData)) {
            return [];
        } else {
            return $aData[0];
        }
    }

    /**
     * 取得會員List
     *
     * @param [type] $_oQuery
     * @param array $_aFields 顯示欄位
     * @param array $_aFieldsCondition 欄位條件(只能相等)
     * @param string $_sOrderBy 欲排序的欄位
     * @param string $_sOrderType 排序的類型 asc | desc
     * @param int $_iLimit 頁數
     * @return void
     */
    public function scopeGetList($_oQuery, $_aFields = [], $_aFieldsCondition=[], $_sOrderBy='id', $_sOrderType = 'asc', $_iLimit = 0, $_aDate = [])
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

        if(!empty($_aDate)) {
            $_oQuery = $_oQuery->whereBetween('modify_datetime', $_aDate);
        }

        $_oQuery = $_oQuery
        ->orderBy($_sOrderBy, $_sOrderType);

        if($_iLimit > 0){//如果有頁數補頁數
            return $_oQuery
            ->select($aFields)
            ->paginate($_iLimit)
            ->appends(['limit' => $_iLimit])
            ->toArray();
        }

        return $_oQuery
        ->get($aFields)
        ->toArray();
    }

    public function scopeUpdateById($_oQuery, $_iId, $_aFieldsCondition)
    {
        $oNow = Carbon::now();
        $sNow = $oNow->toDateTimeString();
        $_aFieldsCondition['modify_datetime'] = $sNow;
        return $_oQuery
        ->where('id', $_iId)
        ->update($_aFieldsCondition);
    }
}
