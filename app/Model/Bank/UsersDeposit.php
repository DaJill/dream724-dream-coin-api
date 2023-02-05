<?php

namespace App\Model\Bank;

use Illuminate\Database\Eloquent\Model;
use App\Model\Admin\AdminUser;

class UsersDeposit extends Model
{
    protected $table = 'users_deposit';
    protected $connection = 'User';

    protected $fillable = [
        'type',
        'no',
        'user_id',
        'payway_id',
        'deposit_account_id',
        'payway_info',
        'deposit_info',
        'credit',
        'payway_status',
        'status',
        'review_admin_id',
        'review_at',
        'reason',
        'deposit_at',
        'ip',
        'third_party_info'
    ];
    protected $casts = [
        'payway_info'      => 'array',
        'deposit_info'     => 'array',
        'third_party_info' => 'array',
    ];


    /**
     * 取存款管理 - 列表總計
     *
     * @param  array  $aQuery
     * @param  array  $aTimeQuery
     *
     * @return object
     */
    public static function getDepositCount($aQuery,$aTimeQuery)
    {
        $result = self::selectRaw('count(1) as count,sum(users_deposit.credit) as total')
            ->where($aQuery)->leftjoin('users', 'users.id', '=', 'users_deposit.user_id');

        // 搜尋日期
        if ((isset($aTimeQuery['start']) && isset($aTimeQuery['end']))) {
            $result->whereBetween('users_deposit.created_at', [$aTimeQuery['start'], $aTimeQuery['end']]);
        }

        return $result->get();
    }

    /**
     * 取存款管理 - 列表
     *
     * @param  array  $aQuery
     * @param  array  $aTimeQuery
     *
     * @return object
     */
    public static function getDepositList($aQuery,$aTimeQuery)
    {
        $result = self::selectRaw('users_deposit.*, users.account, users.nickname, users.mobile')
            ->where($aQuery)->leftjoin('users', 'users.id', '=', 'users_deposit.user_id')->with('admin');

        // 搜尋日期
        if ((isset($aTimeQuery['start']) && isset($aTimeQuery['end']))) {
            $result->whereBetween('users_deposit.created_at', [$aTimeQuery['start'], $aTimeQuery['end']]);
        }

        return $result->orderBy('created_at', 'DESC')->paginate(20);
    }

    /**
     * 取得審核人員資料
     *
     * @return mixed
     */
    public function admin()
    {
        return self::hasOne(AdminUser::class, 'id', 'review_admin_id');
    }

    /**
     * 修改存款管理
     *
     * @param  integer $id
     * @param  array   $parameters
     *
     * @return object
     */
    public static function updateDeposit($id,array $parameters = [])
    {
        return self::find($id)->update($parameters);
    }

    /**
     * 用ID搜尋存款管理訂單
     *
     * @param  integer $id
     *
     * @return object
     */
    public static function getDepositById($id)
    {
        return self::find($id);
    }

}
