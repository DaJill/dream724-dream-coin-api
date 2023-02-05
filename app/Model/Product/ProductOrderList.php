<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Model\User\Users;

class ProductOrderList extends Model
{
    protected $table = 'product_order_list';
    protected $connection = 'Product';

    protected $fillable = [
        'order_id', //訂單ID
        'code', //訂單碼
        'product_id', //募資ID
        'account_id', //訂購人ID
        'name', //寄信名稱
        'mobile', //手機號碼
        'payway_id', //支付方式 0點數, 1銀行, 2第三方
        'address', //地址
        'order_datetime', //訂購時間
        'deadline', //付款截止時間
        'pay_status', //付款狀態 0未付款, 1已付款
        'order_status', //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        'delivery_status', //出貨狀態 0待出貨, 1已出貨, -1出貨取消
        'price_total', //總金額
        'reason', //訂單取消原因 或 出貨取消原因
        'note', //會員備註
        'modify_admin_id', //最後異動管理者
        'callback_code', //外部回call碼
        'modify_datetime', //更新時間

    ];

    public $primaryKey = ['order_id'];
    public $timestamps = false;
    public $incrementing = false; //主鍵自動遞增

    protected $casts = [
        'order_id' => 'integer',
        'code' => 'string',
        'product_id' => 'integer',
        'account_id' => 'integer',
        'name' => 'string',
        'mobile' => 'string',
        'payway_id' => 'integer',
        'address' => 'string',
        'order_datetime' => 'datetime',
        'deadline' => 'datetime',
        'pay_status' => 'integer',
        'order_status' => 'integer',
        'delivery_status' => 'integer',
        'price_total' => 'integer',
        'reason' => 'string',
        'note' => 'string',
        'modify_admin_id' => 'integer',
        'callback_code' => 'string',
        'modify_datetime' => 'datetime',
    ];

    /**
     * Undocumented function
     *
     * @param object $_oQuery
     * @param int $_iAccountID
     * @param string $_sName
     * @param int $_iMobile
     * @param int $_iPaywayId
     * @param string $_sAddress
     * @param int $_iPayStatus
     * @param int $_iOrderStatus
     * @param int $_iDeliveryStatus
     * @param int $_iPriceTotal
     * @param integer $_iDeadLineDay
     * @param string $_sCode
     * @return void
     */

    public function scopeAdd($_oQuery,
    $_iAccountID,
    $_iProductID,
    $_sName,
    $_iMobile,
    $_iPaywayId,
    $_sAddress,
    $_iPayStatus,
    $_iOrderStatus,
    $_iDeliveryStatus,
    $_iPriceTotal,
    $_iDeadLineDay = 1,
    $_sCode,
    $_sNote = null,
    $_sCallbackCode = null
    ){
        $dtNow = Carbon::now()->toDateTimeString();
        $dtDeadLine = Carbon::now()->modify('+'.$_iDeadLineDay.' days')->toDateTimeString();
        return self::insertGetId(
            [
                'account_id' => $_iAccountID,
                'code' => $_sCode,
                'product_id' => $_iProductID,
                'name' => $_sName,
                'mobile' => $_iMobile,
                'payway_id' => $_iPaywayId,
                'address' => $_sAddress,
                'order_datetime' => $dtNow,
                'deadline' => $dtDeadLine,
                'pay_status' => $_iPayStatus,
                'order_status' => $_iOrderStatus,
                'delivery_status' => $_iDeliveryStatus,
                'price_total' => $_iPriceTotal,
                'note' => $_sNote,
                'callback_code' => $_sCallbackCode,
            ]
        );
    }

    /**
     * Undocumented function
     *
     * @param object $_oQuery
     * @param int $_iOrderId 訂單編號
     * @param array $_aFieldsCondition [欄位=>值, ...]參考$this->fillable
     * @return void
     */
    public function scopeUpdateById($_oQuery, $_iOrderId, $_aFieldsCondition)
    {
        $aUpdate = [];
        foreach($_aFieldsCondition as $sCol => $sVal) {
            if($sCol == 'order_id') {
                continue;
            }
            if(in_array($sCol, $this->fillable)) {
                $aUpdate[$sCol] = $sVal;
            }
        }
        $sNow = Carbon::now()->toDateTimeString();
        $aUpdate['modify_datetime'] = $sNow;
        return $_oQuery
        ->where('order_id', $_iOrderId)
        ->update($aUpdate);
    }

    public function scopeGetById($_oQuery, $_iOrderId, $_aFields = [])
    {
        $aCol = empty($_aFields) ? $this->fillable : $_aFields;
        return $_oQuery
        ->select($aCol)
        ->where('order_id', $_iOrderId)
        ->get()
        ->toArray();
    }


    public function scopeGetByCode($_oQuery, $_sCode, $_aFields = [])
    {
        $aCol = empty($_aFields) ? $this->fillable : $_aFields;
        return $_oQuery
            ->select($aCol)
            ->where('code', $_sCode)
            ->with('user:id,account')
            ->get()
            ->toArray();
    }
    /**
     * 綠界繳費到期後更新注單狀態為已取消
     *
     * @param object $_oQuery
     * @param integer $_iOrderId
     * @param array $parameters
     * @param array $_aFields
     *
     * @return mixed
     */
    public function scopeUpdateByMutiId($_oQuery, $_iOrderId, $parameters,$_aFields = [])
    {
        $parameters['modify_datetime'] = Carbon::now()->toDateTimeString();
        $aCol = empty($_aFields) ? $this->fillable : $_aFields;
        return $_oQuery
            ->select($aCol)
            ->whereIn('order_id', $_iOrderId)->update($parameters);
    }
    /**
     * 查詢綠界到期日
     *
     * @param object $_oQuery
     * @param string $_sDate
     * @param array $_aFields
     *
     * @return mixed
     */
    public function scopeGetByDeadline($_oQuery, $_sDate, $_aFields = [])
    {
        $aCol = empty($_aFields) ? $this->fillable : $_aFields;
        return $_oQuery
            ->select($aCol)
            ->where('payway_id', '!=' ,3)
            ->where('deadline', '<' ,$_sDate)
            ->where('pay_status',0)
            ->where('order_status',0)
            ->get();
    }
    /**
     * 驗證該會員是否有尚未處理訂單 [銀行卡]
     *
     * @param object $_oQuery
     * @param integer $_iPaywayId
     * @param integer $_iAccountId
     * @param array $_aFields
     *
     * @return mixed
     */
    public function scopeGetStatus($_oQuery, $_iPaywayId, $_iAccountId, $_aFields = [])
    {
        $aCol = empty($_aFields) ? $this->fillable : $_aFields;
        $condition = ['payway_id' => $_iPaywayId,'account_id' => $_iAccountId,'order_status' => 0];
        return $_oQuery
            ->select($aCol)
            ->where($condition)
            ->exists();
    }

    /**
     * 取得審核人員資料
     *
     * @return mixed
     */
    public function user()
    {
        return self::hasOne(Users::class, 'id', 'account_id');
    }

    /**
     * 刪除綠界暫存訂單
     *
     * @return mixed
     */
    public function scopeDeleteMutiOrderId($_oQuery, $_aOrderId)
    {
        return $_oQuery->with('user')->wherehas('user', function ($q) {
            $q->from('UserDB.users')->where('hallid', 2);
        })->whereIn('order_id', $_aOrderId)->delete();
    }

    public function scopeGetRaisedByProductId($_oQuery, $_aProductId)
    {
        return $_oQuery->groupBy('product_id')
        ->selectRaw('product_id, sum(price_total) as raised, count(*) as raised_people')
        ->whereIn('product_id', $_aProductId)
        ->get()
        ->toArray();
    }

    /**
     * 取得list
     *
     * @param [type] $_oQuery
     * @param array $_aFieldsCondition 欄位條件[key => val, ...]
     * @param array $_aFields 顯示欄位
     * @param array $_aDate 日期欄位
     * @param int $_iLimit 頁數
     * @return void
     */
    public function scopeGetList($_oQuery, $_aFieldsCondition = [], $_aFields = [], $_aDate = [], $_iLimit = 0)
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        $_oQuery = $_oQuery
        ->select($aFields);
        foreach($_aFieldsCondition as $sCol => $mVal) {
            if(in_array($sCol, $this->fillable)) {
                $_oQuery = is_array($mVal) ? $_oQuery->whereIn($sCol, $mVal) : $_oQuery->where($sCol, $mVal);
            }
        }

        if(!empty($_aDate)) {
            $_oQuery = $_oQuery->whereBetween('modify_datetime', $_aDate);
        }

        //如果帶account，必須用user的table才取得到
        if(isset($_aFieldsCondition['account'])) {
            $_oQuery = $_oQuery->with('user')->wherehas('user', function ($q) use ($_aFieldsCondition) {
                $q->from('UserDB.users')->where('account', $_aFieldsCondition['account']);
            });
        }

        $_oQuery = $_oQuery->orderBy('modify_datetime', 'desc');
        if($_iLimit > 0){//如果有頁數補頁數
            return $_oQuery
            ->select($aFields)
            ->paginate($_iLimit)
            ->appends(['limit' => $_iLimit])
            ->toArray();
        }

        return $_oQuery
        ->get()
        ->toArray();
    }
}
