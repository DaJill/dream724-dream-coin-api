<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Api\Payment\Services\Pay1177Services;
use App\Api\Payment\Services\PaymentServices;
use App\Api\Log\Traits\LogTraits;

class Pay1177 extends Controller
{
    use LogTraits;

    protected $pay1177Services;
    protected $paymentServices;

    public function __construct(Pay1177Services $pay1177Services, PaymentServices $paymentServices)
    {
        $this->pay1177Services = $pay1177Services;
        $this->paymentServices = $paymentServices;
    }

    /**
     * 付款完成通知回傳的網址 - 1177 callBack
     *
     * @param Request $request
     *
     * @return mixed
     */

    public function notice(Request $request)
    {
        try {

            $result['rc'] = $request['rc'];
            $noticeInfo = $this->pay1177Services->notice($request->all());
            if ($noticeInfo['result'] !== true || $result['rc'] != 0) {
                $error = ['request' => $request->all(),'error' => $noticeInfo['error']];
                $this->createLog('1177Notice',$error);
            }

            return response()->json($result['rc']);
        } catch (\Exception $e) {
            $error = ['request' => $request->all(),'error' => $e->getMessage()];
            $this->createLog('1177Notice2',$error);
            return [
                'result' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }

    /**
     * 測試
     *
     * @param Request $request
     *
     * @return object
     */
    public function order(Request $request)
    {
        return $this->paymentServices->createOrder($request->all());
    }

   public function test()
   {
       $r['amount'] = 1000;
       $r['payway_id'] = 1;
       //$r['code'] = 'AA'.rand(10000,99999);
       $r['code'] = 'HEtlsm11';

       return $this->pay1177Services->createPay1177Order($r);
   }
}
