<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Model\Product\ProductOrderList;

class UsersDepositPayment extends Model
{
    protected $table = 'users_deposit_payment';
    protected $connection = 'Product';
    protected $fillable = [
        'order_id',
        'payway_id',
        'deposit_account_id',
        'payway_info',
        'deposit_info',
        'credit',
        'payment_info'
    ];
    protected $casts = [
        'order_id'           => 'integer',
        'payway_id'          => 'integer',
        'deposit_account_id' => 'integer',
        'payway_info'        => 'array',
        'deposit_info'       => 'array',
        'credit'             => 'integer',
        'payment_info'       => 'array',
    ];


    /**
     * 新增會員存款訂單 - 銀行資訊
     *
     * @param  array   $parameters
     *
     * @return object
     */
    public static function createPayment(array $parameters = [])
    {
        return self::create($parameters);
    }


    /**
     * 更新會員存款訂單 - 銀行資訊
     *
     * @param  integer $id
     * @param  array   $parameters
     *
     * @return object
     */
    public static function updatePayment($id,array $parameters = [])
    {
        return self::find($id)->update($parameters);
    }


    /**
     * 搜尋指定的會員存款訂單 - 銀行資訊
     *
     * @param  integer $id
     *
     * @return object
     */
    public static function getPaymentByOrderId($id)
    {
        return self::where('order_id',$id)->get();
    }

    /**
     * 取得主訂單資訊
     *
     * @return mixed
     */
    public function order()
    {
        return self::hasOne(ProductOrderList::class, 'order_id', 'order_id');
    }

    /**
     * 搜尋指定的會員存款訂單 - 銀行資訊 by 訂單狀態
     *
     * @param  integer $id
     *
     * @return object
     */
    public static function getPaymentByStatus($id)
    {
        return self::with('order')->wherehas('order', function ($q) {
            $q->from('product_order_list');
        })->where('order_id',$id)->get();

    }

    /**
     * 取得刪除暫存的存款訂單ID
     *
     * @param  datetime $time
     * @return integer
     */
    public static function getdeletePaymentId($time)
    {
        return self::where('created_at', '<=', $time)
            ->whereColumn('created_at','=','updated_at')
            ->whereIn('payway_id', [1, 2])
            ->pluck('order_id')->toArray();
    }

}
