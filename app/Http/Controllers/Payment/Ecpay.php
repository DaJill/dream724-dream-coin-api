<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Api\Payment\Services\EcpayServices;
use App\Api\Payment\Services\PaymentServices;
use App\Api\Log\Traits\LogTraits;

class Ecpay extends Controller
{
    use LogTraits;

    protected $ecpayServices;
    protected $paymentServices;

    public function __construct(EcpayServices $ecpayServices,PaymentServices $paymentServices)
    {
        $this->ecpayServices = $ecpayServices;
        $this->paymentServices = $paymentServices;
    }


    /**
     * 綠界產生訂單
     *
     * @param Request $request
     *
     * @return array
     */

    public function create(Request $request)
    {
        return $this->ecpayServices->createEcpayOrder($request->all());
    }

    /**
     * Server 端回傳 付款相關資訊
     *
     * @param Request $request
     *
     * @echo result
     */

    public function receive(Request $request)
    {
        try {
            $this->createLog('ecpayReceiveRequest',['request' => $request->all()]);

            $result = '1|OK';
            $receiveInfo = $this->ecpayServices->receiveInfo($request->all());

            if ($receiveInfo['result'] !== true) {
                $error = ['request' => $request->all(),'error' => $receiveInfo['error']];
                $this->createLog('ecpayReceive',$error);
                $result = '0|FALSE';
            }

            echo $result;
            exit;
        } catch (\Exception $e) {
            $error = ['request' => $request->all(),'error' => $e->getMessage()];
            $this->createLog('ecpayReceive2',$error);
            echo '0|FALSE';
            exit;
        }
    }
    /**
     * 付款完成通知回傳的網址 - 綠界callBack
     *
     * @param Request $request
     *
     * @echo object
     */

    public function notice(Request $request)
    {
        try {
            $this->createLog('ecpayNoticeRequest',['request' => $request->all()]);

            if($request['RtnCode'] != '1'){
                $this->createLog('ecpayNoticeCheckError',['request' => $request->all()]);
                echo '0|MacValueNotMatch';
                exit;
            }

            $result = '1|OK';
            $noticeInfo = $this->ecpayServices->notice($request->all());
            if ($noticeInfo['result'] !== true) {
                $error = ['request' => $request->all(),'error' => $noticeInfo['error']];
                $this->createLog('ecpayNotice',$error);
                $result = '0|FALSE';
            }

            echo $result;
            exit;
        } catch (\Exception $e) {
            $error = ['request' => $request->all(),'error' => $e->getMessage()];
            $this->createLog('ecpayNotice2',$error);
            echo '0|FALSE';
            exit;
        }
    }

    /**
     * 導轉URL
     *
     * @param Request $request
     *
     * @return object
     */
    public function redirection(Request $request)
    {
        $url = 'https://'.$request['domain'];
        $no = $request['xno'];
        $redirectUrl = $url.'/transaction/deposit/done/'.$no;

        return redirect()->away($redirectUrl , 301);
    }

}
