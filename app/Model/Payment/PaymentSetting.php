<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $table = 'payment_setting';
    protected $connection = 'User';
    protected $casts    = ['type' => 'string'];

    /**
     * 取得第三方支付類型(1：綠界，2：1177pay)
     *
     * @return object
     */
    public static function getPaymentSetting()
    {
        return self::get(['type']);
    }

}
