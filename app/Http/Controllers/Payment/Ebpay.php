<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Api\Payment\Services\EbpayServices;
use App\Api\Payment\Services\PaymentServices;
use App\Api\Log\Traits\LogTraits;
use App\Model\Payment\EbpaySetting;

class Ebpay extends Controller
{
    use LogTraits;

    protected $ebpayServices;
    protected $paymentServices;
    protected $ebpaySettingModel;

    public function __construct(EbpayServices $ebpayServices,PaymentServices $paymentServices,EbpaySetting $ebpaySettingModel)
    {
        $this->ebpayServices = $ebpayServices;
        $this->paymentServices = $paymentServices;
        $this->ebpaySettingModel = $ebpaySettingModel;
    }


    /**
     * 藍新產生訂單
     *
     * @param Request $request
     *
     * @return array
     */

    public function create(Request $request)
    {
        return $this->ebpayServices->createEbpayOrder($request->all());
    }

    /**
     * Server 端回傳 付款相關資訊
     *
     * @param Request $request
     *
     * @return array
     */

    public function receive(Request $request)
    {
        try {
            $this->createLog('ebpayReceiveRequest',['request' => $request->all()]);
            $receiveInfo = $this->ebpayServices->receiveInfo($request->all());

            if ($receiveInfo['result'] !== true) {
                $error = ['request' => $request->all(),'error' => $receiveInfo['error']];
                $this->createLog('ebpayReceive',$error);
            }

            return redirect()->away($receiveInfo['data'],301);

        } catch (\Exception $e) {
            $error = ['request' => $request->all(),'error' => $e->getMessage()];
            $this->createLog('ebpayReceive2',$error);
            return $error;
        }
    }
    /**
     * 付款完成通知回傳的網址 - callBack
     *
     * @param Request $request
     *
     * @return null;
     */

    public function notice(Request $request)
    {
        try {
            $this->createLog('ebpayNoticeRequest',['request' => $request->all()]);

            if($request['Status'] != 'SUCCESS'){
                $this->createLog('ebpayNoticeCheckError',['request' => $request->all()]);
                return $result = false;
            }

            $noticeInfo = $this->ebpayServices->notice($request->all());
            if ($noticeInfo['result'] !== true) {
                $error = ['request' => $request->all(),'error' => $noticeInfo['error']];
                $this->createLog('ebpayNotice',$error);
            }

        } catch (\Exception $e) {
            $error = ['request' => $request->all(),'error' => $e->getMessage()];
            $this->createLog('ebpayNotice2',$error);
        }
    }

}
