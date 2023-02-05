<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Model\Payment\EcpayDomain;

class EcpaySetting extends Model
{
    protected $table = 'ecpay_setting';
    protected $connection = 'User';

    protected $casts = [
        'merchant_id'     => 'string',
        'hash_key'        => 'string',
        'hash_iv'         => 'string',
        'api_url'         => 'string',
        'ecpay_domain_id' => 'integer',
        'active'          => 'integer',
    ];

    /**
     * 取得綠界金鑰資訊
     *
     * @return object
     */
    public function getEcpaySetting()
    {
        return self::where('active', '1')->with('domain')->get();
    }


    /**
     * 取得綠界domain
     *
     * @return object
     */
    public function domain()
    {
        return self::hasOne(EcpayDomain::class, 'id', 'ecpay_domain_id')->where('active', '1')->select(['id', 'url']);
    }
}
