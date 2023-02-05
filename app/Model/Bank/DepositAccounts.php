<?php

namespace App\Model\Bank;

use Illuminate\Database\Eloquent\Model;

class DepositAccounts extends Model
{
    protected $table = 'credit_deposit_account';
    protected $connection = 'User';

    protected $fillable = ['bank_id', 'code', 'branch', 'account', 'name', 'active', 'debit', 'credit'];
    protected $casts = [
        'bank_id' => 'integer',
        'code'    => 'string',
        'branch'  => 'string',
        'account' => 'string',
        'name'    => 'string',
        'active'  => 'integer',
        'debit'   => 'decimal:4',
        'credit'  => 'decimal:4',
    ];

    /**
     * 取存款帳戶管理 - 列表
     *
     * @return object
     */
    public static function getDepositAccounts()
    {
        return self::orderBy('id','ASC')->paginate(15);
    }

    /**
     * 取啟用中的存款帳戶
     *
     * @return object
     */
    public static function getEnableDepositAccounts()
    {
        $aData = self::where('active',1)->get();
        if(empty($aData)) {
            return [];
        } else {
            return $aData[0];
        }
    }

    /**
     * 新增存款帳戶
     *
     * @param  array   $parameters
     *
     * @return object
     */
    public static function createDepositAccount(array $parameters = [])
    {
        return self::create($parameters);
    }

    /**
     * 修改存款帳戶
     *
     * @param  integer $id
     * @param  array   $parameters
     *
     * @return object
     */
    public static function updateDepositAccount($id,array $parameters = [])
    {
        return self::find($id)->update($parameters);
    }

    /**
     * 檢查銀行帳戶是否存在
     *
     * @param  string $bankId  銀行資料id
     * @param  string $account 銀行帳號
     * @param  integer $id     本身帳號排除
     *
     * @return object
     */
    public static function checkDepositAccount($bankId,$account,$id = 0)
    {
        if(empty($id)){
            return self::where('bank_id', $bankId)->where('account', $account)->exists();
        }else{
            return self::where('bank_id', $bankId)->where('account', $account)->where('id', '!=', $id)->exists();
        }

    }

    /**
     * 搜尋指定的存款帳戶管理資料
     *
     * @param  integer $id
     *
     * @return object
     */
    public static function getDepositAccountsById($id)
    {
        return self::find($id)->toArray();
    }

    /**
     * 刪除存款帳戶管理
     *
     * @param  integer $id
     *
     * @return string
     */
    public static function deleteDepositAccount($id)
    {
        return self::destroy($id);
    }

    /**
     * 停用開啟的帳戶
     *
     * @return object
     */
    public static function disableDepositAccount()
    {
        return self::where('active', 1)->update(['active' => 2]);
    }

    /**
     * 扣除審核中存點
     *
     * @param  integer $id     [存款帳戶ID]
     * @param  decimal $credit [金額]
     * @return array
     */

    public static function updateDebit($id,$credit)
    {
        return self::find($id)->decrement('debit', $credit);
    }

}
