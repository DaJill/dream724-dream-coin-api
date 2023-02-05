<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;

class EbpaySetting extends Model
{
    protected $table = 'ebpay_setting';
    protected $connection = 'User';

    protected $casts = [
        'merchant_id'     => 'string',
        'hash_key'        => 'string',
        'hash_iv'         => 'string',
        'api_url'         => 'string',
        'active'          => 'integer',
    ];

    /**
     * 取得藍新金鑰資訊
     *
     * @return object
     */
    public function getSetting()
    {
        return self::where('active', '1')->get();
    }

}
