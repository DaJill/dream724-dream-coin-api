<?php

namespace App\Api\Payment\Services;

use App\Model\Payment\UsersDepositPayment;
use App\Model\Bank\Banks;
use App\Model\Product\ProductOrderList;
use GuzzleHttp\Client;
use App\Api\Log\Traits\LogTraits;
use DB;
use Carbon\Carbon;
use App\Api\Payment\Services\PlatformServices;


class Pay1177Services
{
    use LogTraits;

    private $paymentType = [1 => 'mmk', 2 => 'atm'];
    private $host = 'https://uat.1177tech.com.tw';
    private $merchantnumber = '118455'; //商店編號
    private $transcode = 'abcd1234'; //商店編號
    protected $platformServices;

    public function __construct(PlatformServices $platformServices)
    {
        $this->platformServices = $platformServices;
    }

    /**
     * 產生1177 Pay訂單
     *
     * @param  Request $request [order_id,code,payway_id,name,amount]
     * @return mixed
     */
    public function createPay1177Order($request)
    {
        try {
            $url = 	$this->host.'/1177payment/api/acceptpayment';
            $amount = intval($request['amount']);

            $seed['amount'] = $amount;
            if($request['payway_id'] == 2) $seed['bankid'] = $request['bankid'];
            $seed['merchantnumber'] = $this->merchantnumber;
            $seed['ordernumber'] = $request['code'];
            $seed['paymenttype'] = $this->paymentType[$request['payway_id']];
            $seed['paytitle'] = 'test';
            $seed['timestamp'] = Carbon::now()->format('YmdHis');
            $checksum = strtoupper(hash('sha256', http_build_query($seed).'abcd1234'));
            $seed['checksum'] = $checksum;
            //return $seed;
            $client = new Client();
            $response = $client->post($url, [
                'form_params' =>
                    $seed
                ,
            ]);
            $response_decode = json_decode($response->getBody(),true);
            $this->receiveInfo($response_decode);
            return $response_decode;
        } catch (\Exception $e) {
            return [
                'result'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Server 端回傳 付款相關資訊
     *
     * @param  array $request
     * @return mixed
     */
    public function receiveInfo($request)
    {
        try {
            $result = DB::transaction(function () use ($request) {

                $order = ProductOrderList::getByCode($request['ordernumber']);

                if ($order == null) {
                    return false;
                }

                $payment = UsersDepositPayment::getPaymentByOrderId($order[0]['order_id']);
                $paymentInfo = $payment[0]['payment_info'];
                $paymentInfo['receive'] = $request;
                $payway_info = [];
                $deadline = [];

                switch ($order[0]['payway_id']) {
                    case 1:
                        $payway_info = [
                            'member'     => $order[0]['user']['account'], // 會員帳號
                            'payment_no' => $request['paycode'], // 超商繳費代碼
                            'no'         => $request['paymentnumber'], // 付款序號
                            'expire_at'  => Carbon::parse($request['duedate'])->toDateTimeString(), // 允許繳費有效時間
                            'credit'     => $order[0]['price_total'], // 金額
                        ];
                        $deadline = $payway_info['expire_at'];
                        break;
                    case 2:
                        $bank = Banks::getBanksCodeIndex();
                        $payway_info = [
                            'member'       => $order[0]['user']['account'], // 會員帳號
                            'code'         => $request['bankid'], // 繳費銀行代碼
                            'bank'         => $bank[$request['bankid']]['name'], // 銀行
                            'bank_account' => $request['virtualaccount'], // 繳費虛擬帳號
                            'no'         => $request['paymentnumber'], // 付款序號
                            'expire_at'    => Carbon::parse($request['duedate'])->toDateTimeString(), // 允許繳費有效時間
                            'credit'       => $order[0]['price_total'], // 金額
                        ];
                        $deadline = $payway_info['expire_at'];
                        break;
                    default:
                }

                $update = [
                    'payway_info' => $payway_info,
                    'payment_info' => $paymentInfo,
                ];

                // 更新會員存款訂單 - 銀行資訊 & 到期日
                ProductOrderList::updateById($order[0]['order_id'], ['deadline' => $deadline]);
                UsersDepositPayment::updatePayment($payment[0]['id'], $update);

                return true;
            });
            return [
                'result' => $result
            ];
        } catch (\Exception $e) {
            return [
                'result' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }

    /**
     * 付款完成通知回傳
     *
     * @param  array $request
     * @return mixed
     */
    public function notice($request)
    {
        try {
            $order = ProductOrderList::getByCode($request['ordernumber']);

            if ($order == null) {
                return false;
            }
            // 更新會員主訂單狀態 - 已付款
            ProductOrderList::updateById($order[0]['order_id'], ['pay_status' => 1 ,'order_status' => 1]);
            $payment = UsersDepositPayment::getPaymentByOrderId($order[0]['order_id']);
            $paymentInfo = $payment[0]['payment_info'];
            $paymentInfo['notice'] = $request;

            $result = DB::transaction(function () use ($payment,$paymentInfo,$order) {
                $update = ['payment_info' => $paymentInfo];

                // 更新會員存款訂單 - 銀行資訊
                UsersDepositPayment::updatePayment($payment[0]['id'], $update);
                if(!empty($order[0]['callback_code'])) {
                    $this->platformServices->transfer($order[0]['user']['account'], $order[0]['price_total'],
                        $order[0]['callback_code']);
                }
                return true;
            });
            return [
                'result' => $result
            ];
        } catch (\Exception $e) {
            return [
                'result' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }
}
