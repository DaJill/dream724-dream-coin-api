<?php

namespace App\Api\Payment\Services;

use App\Model\Payment\EcpaySetting;
use App\Model\Payment\UsersDepositPayment;
use App\Model\Bank\Banks;
use App\Model\Product\ProductOrderList;
use App\Api\Payment\Services\PlatformServices;
use App\Api\Log\Traits\LogTraits;
use DB;
use Carbon\Carbon;


class EcpayServices
{
    use LogTraits;

    private $paymentType = [1 => 'CVS', 2 => 'ATM'];
    protected $ecpaySettingModel;
    protected $platformServices;

    public function __construct(
        EcpaySetting $ecpaySettingModel, PlatformServices $platformServices
    ) {
        include_once('ECPayPaymentIntegration.php');
        $this->ecpaySettingModel = $ecpaySettingModel;
        $this->platformServices = $platformServices;
    }

    /**
     * 產生綠界訂單
     *
     * @param  Request $request [order_id,code,payway_id,name,amount,callback_code]
     * @return mixed
     */
    public function createEcpayOrder($request)
    {
        try {
            $setting = $this->ecpaySettingModel->getEcpaySetting()[0];

            $obj = new ECPay_AllInOne();
            $obj->ServiceURL = $setting['api_url'] . '/SP/CreateTrade';
            $obj->HashKey = $setting['hash_key'];
            $obj->HashIV = $setting['hash_iv'];
            $obj->MerchantID = $setting['merchant_id'];
            $obj->EncryptType = 1;
            $amount = intval($request['amount']);
            $desc = '商品購買';

            // 訂單基本參數
            $obj->Send['MerchantTradeNo'] = $request['code'];                 //訂單編號
            $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');            //交易時間(格式：yyyy/MM/dd HH:mm:ss)
            $obj->Send['PaymentType'] = 'aio';                                //交易類型(固定填入 aio)
            $obj->Send['TotalAmount'] = $amount;                              //交易金額
            $obj->Send['TradeDesc'] = $desc;                                  //交易描述
            $obj->Send['ReturnURL'] = $setting['domain']['url'] . '/api/ecpay/notice'; //付款完成通知回傳的網址
            $obj->Send['PaymentInfoURL'] = $setting['domain']['url'] . '/api/ecpay/receive'; //回傳付款相關資訊
            $obj->Send['ChoosePayment'] = 'ALL';
            $obj->Send['NeedExtraPaidInfo'] = 'Y';
            $ClientBackURL = config('api.url.dream').'/order/finish/'.$request['code'];
            $domain = $request['domain'] ? parse_url($request['domain'],PHP_URL_HOST) : 'qxin168.net';
            $PayURL = config('api.url.pay').'/api/ecpay/index?domain='.$domain.'%26xno='.$request['callback_code'];
            $obj->Send['ClientBackURL'] = ($request['callback_code'] === null)?$ClientBackURL:$PayURL; //Client端返回按鈕連結

            // 設置付款方式的繳費期限
            switch ($request['payway_id']) {
                case 1: // 超商代碼 - 分鐘
                    $obj->Send['StoreExpireDate'] = 1440;
                    break;
                case 2: // ATM轉帳 - 天數
                    $obj->Send['ExpireDate'] = 1;
                    break;
                default:
            }
            //訂單的商品資料
            array_push($obj->Send['Items'], [
                'Name'     => $desc,
                'Price'    => $amount,
                'Currency' => '元',
                'Quantity' => 1,
            ]);
            // 產生綠界訂單
            $result = $obj->CreateTrade();
            if ($result['RtnCode'] != '1') {
                // todo: 記LOG
                $error = ['request' => $request,'error' => 'ecpay error'];
                $this->createLog('ecpayCreate',$error);
                return [
                    'result'  => false,
                    'message' => 'ecpay error',
                ];
            }

            // 新增 payway_info
            switch ($request['payway_id']) {
                case 1:
                    $deposit['payway_info'] = [
                        'member'     => '', // 會員帳號
                        'title'      => '超商付款',
                        'payment_no' => '', // 超商繳費代碼
                        'no'         => '', // 綠界交易編號
                        'expire_at'  => '', // 允許繳費有效時間
                        'credit'     => '', // 金額
                    ];
                    break;
                case 2:
                    $deposit['payway_info'] = [
                        'member'       => '', // 會員帳號
                        'code'         => '', // 繳費銀行代碼
                        'bank'         => '', // 銀行
                        'bank_account' => '', // 繳費虛擬帳號
                        'no'           => '',  // 綠界交易編號
                        'expire_at'    => '', // 允許繳費有效時間
                        'credit'       => '', // 金額
                    ];
                    break;
                default:
            }


            // 新增會員存款訂單 - 銀行資訊
            $deposit = [
                'order_id'     => $request['order_id'],
                'payway_id'    => $request['payway_id'],
                'credit'       => $amount,
                'payment_info' => ['create' => $result]
            ];
            UsersDepositPayment::create($deposit);


            // 組成要支付的連結
            $ecpayURL = $setting['api_url'] . '/SP/SPCheckOut?MerchantID=' . $setting['merchant_id'];
            $ecpayURL .= '&SPToken=' . $result['SPToken'] . '&PaymentType=' . $this->paymentType[$request['payway_id']];

            return [
                'result' => true,
                'data'   => ['url' => $ecpayURL],
            ];
        } catch (\Exception $e) {
            $error = ['request' => $request,'error' => $e->getMessage()];
            $this->createLog('ecpayCreate',$error);
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

                $order = ProductOrderList::getByCode($request['MerchantTradeNo']);

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
                            'payment_no' => $request['PaymentNo'], // 超商繳費代碼
                            'no'         => $request['TradeNo'], // 綠界交易編號
                            'expire_at'  => strtr($request['ExpireDate'], '/', '-'), // 允許繳費有效時間
                            'credit'     => $order[0]['price_total'], // 金額
                        ];
                        $deadline = $request['ExpireDate'];
                        break;
                    case 2:
                        $bank = Banks::getBanksCodeIndex();
                        $payway_info = [
                            'member'       => $order[0]['user']['account'], // 會員帳號
                            'code'         => $request['BankCode'], // 繳費銀行代碼
                            'bank'         => $bank[$request['BankCode']]['name'], // 銀行
                            'bank_account' => $request['vAccount'], // 繳費虛擬帳號
                            'no'           => $request['TradeNo'],  // 綠界交易編號
                            'expire_at'    => strtr($request['ExpireDate'], '/', '-'), // 允許繳費有效時間
                            'credit'       => $order[0]['price_total'], // 金額
                        ];
                        $deadline = $request['ExpireDate'].' 23:59:59';
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
                if(!empty($order[0]['callback_code'])) {
                    $this->platformServices->transferInfo($order[0]['callback_code'],$payway_info);
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

    /**
     * 付款完成通知回傳的網址
     *
     * @param  array $request
     * @return mixed
     */
    public function notice($request)
    {
        try {
            $order = ProductOrderList::getByCode($request['MerchantTradeNo']);

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

    /**
     * 綠界的檢查碼製作
     *
     * @param  array $request
     * @return mixed
     */
    public function generateCheckValue($request)
    {
        $setting = $this->ecpaySettingModel->getEcpaySetting()[0];

        return ECPay_CheckMacValue::generate($request, $setting['hash_key'], $setting['hash_iv'], ECPay_EncryptType::ENC_SHA256);
    }

    /**
     * 檢查綠界已過期訂單
     *
     * @return mixed
     */

    public function checkExpire()
    {
        try {
            $ids = [];
            $xinId = [];
            $date = Carbon::now()->toDateTimeString();

            $deadline = ProductOrderList::getByDeadline($date);

            foreach ($deadline as $value) {
                $ids[] = $value['order_id'];
                if(!empty($value['callback_code'])) $xinId[] = $value['callback_code'];
            }

            if (count($ids) > 0) {
                ProductOrderList::UpdateByMutiId($ids, [
                    'order_status'    => -1,
                    'reason'          => '過期已取消',
                ]);
            }
            // 取消平台逾期儲值單
            if (count($xinId) > 0) {
                $this->platformServices->cancel($xinId);
            }

            return [
                'result' => true,
                'data'   => [],
            ];
        } catch (\Exception $e) {
            return [
                'result' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }

    /**
     * 刪除綠界暫存訂單 [20分鐘]
     *
     * @return mixed
     */

    public function deleteOrder()
    {
        try {
            $time = Carbon::now()->subMinutes(20)->toDateTimeString();
            $orderId = UsersDepositPayment::getdeletePaymentId($time);

            if (count($orderId) > 0) {
                ProductOrderList::deleteMutiOrderId($orderId);
            }
            return [
                'result' => true,
                'data'   => [],
            ];
        } catch (\Exception $e) {
            return [
                'result' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }

}
