<?php

namespace App\Model\Payment;

use Illuminate\Database\Eloquent\Model;

class EcpayDomain extends Model
{
    protected $table = 'ecpay_domain';
    protected $connection = 'User';

    protected $casts    = ['url' => 'string', 'active' => 'integer', 'sort' => 'integer'];


}
