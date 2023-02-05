<?php

namespace App\Api\Payment\Services;

use App\Model\Payment\EbpaySetting;
use App\Model\Payment\UsersDepositPayment;
use App\Model\Bank\Banks;
use App\Model\Product\ProductOrderList;
use App\Api\Payment\Services\PlatformServices;
use App\Api\Log\Traits\LogTraits;
use DB;
use Carbon\Carbon;


class EbpayServices
{
    use LogTraits;

    protected $ebpaySettingModel;
    protected $platformServices;
    private $paymentType = [1 => 'CVS', 2 => 'ATM'];
    private $domain = [1 => 'https://qxin168.net', 2 => 'https://qxin888.net'];
    private $ver = '1.5';

    public function __construct(EbpaySetting $ebpaySettingModel,PlatformServices $platformServices)
    {
        $this->ebpaySettingModel = $ebpaySettingModel;
        $this->platformServices = $platformServices;
    }

    /**
     * 產生訂單
     *
     * @param  Request $request [order_id,code,payway_id,name,amount,callback_code,domain]
     * @return mixed
     */
    public function createEbpayOrder($request)
    {
        try {
            $setting = $this->ebpaySettingModel->getSetting()[0];

            $trade_info_arr = [];
            $amount = intval($request['amount']);
            $flipped = array_flip($this->domain);
            $domainId = (!empty($request['domain'])) ? $flipped[$request['domain']] : 1;
            $receiveUrl = $setting['notice_url'].'/api/ebpay/receive';
            $ClientBackURL = ($request['callback_code'] === null)?$receiveUrl:$receiveUrl.'?domain_id='.$domainId;
            /* 送給藍新資料 */
            $trade_info_arr = array(
                'MerchantID'      => $setting['merchant_id'],
                'RespondType'     => 'JSON',
                'TimeStamp'       => time(),
                'Version'         => $this->ver,
                'MerchantOrderNo' => $request['code'],
                'Amt'             => $amount,
                'Email'           => 'qxincservice@gmail.com',
                'EmailModify'     => 0,
                'ItemDesc'        => '商品購買',
                'NotifyURL'       => $setting['notice_url'].'/api/ebpay/notice', //支付通知網址
                'CustomerURL'     => $ClientBackURL, //商店取號網址
                'ExpireDate'      => Carbon::tomorrow()->format('Y-m-d'),
            );

            // 設置付款方式的繳費期限
            switch ($request['payway_id']) {
                case 1: // 超商代碼 - 分鐘
                    $trade_info_arr['CVS'] = 1;
                    break;
                case 2: // ATM轉帳 - 天數
                    $trade_info_arr['VACC'] = 1;
                    break;
                default:
            }

            // 新增 payway_info
            switch ($request['payway_id']) {
                case 1:
                    $deposit['payway_info'] = [
                        'member'     => '', // 會員帳號
                        'title'      => '超商付款',
                        'payment_no' => '', // 超商繳費代碼
                        'no'         => '', // 交易編號
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
                        'no'           => '', // 交易編號
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
                'payment_info' => ['create' => true]
            ];
            UsersDepositPayment::create($deposit);

            $tradeInfo = $this->create_mpg_aes_encrypt($trade_info_arr, $setting['hash_key'], $setting['hash_iv']);
            $SHA256 = strtoupper(hash("sha256", $this->SHA256($setting['hash_key'], $tradeInfo, $setting['hash_iv'])));

            return [
                'result' => true,
                'data'   => [
                    'url'           => $setting['api_url'],
                    'MerchantID'    => $setting['merchant_id'],
                    'TradeInfo'     => $tradeInfo,
                    'TradeSha'      => $SHA256,
                    'Version'       => $this->ver,
                ],
            ];
        } catch (\Exception $e) {
            $error = ['request' => $request,'error' => $e->getMessage()];
            $this->createLog('ebpayCreate',$error);
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
            $setting = $this->ebpaySettingModel->getSetting()[0];
            $tradeInfo = trim($request['TradeInfo']);
            $data = $this->decrypt($tradeInfo,$setting['hash_key'], $setting['hash_iv']);
            $data = $data['Result'];

            $result = DB::transaction(function () use ($data,$request) {

                $order = ProductOrderList::getByCode($data['MerchantOrderNo']);

                if ($order == null) {
                    return false;
                }

                $payment = UsersDepositPayment::getPaymentByOrderId($order[0]['order_id']);
                $paymentInfo = $payment[0]['payment_info'];
                $paymentInfo['receive'] = $data;
                $payway_info = [];
                $deadline = [];
                $deadline = $data['ExpireDate'] . ' 23:59:59';

                switch ($order[0]['payway_id']) {
                    case 1:
                        $payway_info = [
                            'member'     => $order[0]['user']['account'], // 會員帳號
                            'payment_no' => $data['CodeNo'], // 超商繳費代碼
                            'no'         => $data['TradeNo'], // 交易編號
                            'expire_at'  => $deadline, // 允許繳費有效時間
                            'credit'     => $order[0]['price_total'], // 金額
                        ];
                        break;
                    case 2:
                        $bank = Banks::getBanksCodeIndex();
                        $payway_info = [
                            'member'       => $order[0]['user']['account'], // 會員帳號
                            'code'         => $data['BankCode'], // 繳費銀行代碼
                            'bank'         => $bank[$data['BankCode']]['name'], // 銀行
                            'bank_account' => $data['CodeNo'], // 繳費虛擬帳號
                            'no'           => $data['TradeNo'],  // 交易編號
                            'expire_at'    => $deadline, // 允許繳費有效時間
                            'credit'       => $order[0]['price_total'], // 金額
                        ];
                        break;
                    default:
                }

                $update = [
                    'payway_info'  => $payway_info,
                    'payment_info' => $paymentInfo,
                ];

                // 更新會員存款訂單 - 銀行資訊 & 到期日
                ProductOrderList::updateById($order[0]['order_id'], ['deadline' => $deadline]);
                UsersDepositPayment::updatePayment($payment[0]['id'], $update);
                if(!empty($order[0]['callback_code'])) {
                    $this->platformServices->transferInfo($order[0]['callback_code'],$payway_info);
                }
                $domain = (!empty($request['domain_id'])) ? $this->domain[$request['domain_id']] : 'https://qxin168.net';
                $PayURL = $domain.'/transaction/deposit/done/'.$order[0]['callback_code'];
                $ClientBackURL = ($order[0]['callback_code'] === null)?config('api.url.dream').'/order/finish/'.$order[0]['code']:$PayURL;

                return [
                    'result' => true,
                    'data'   => $ClientBackURL
                ];
            });
            return [
                'result' => $result['result'],
                'data'   => $result['data'],
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
            $setting = $this->ebpaySettingModel->getSetting()[0];
            $tradeInfo = trim($request['TradeInfo']);
            $data = $this->decrypt($tradeInfo,$setting['hash_key'], $setting['hash_iv']);
            $data = $data['Result'];
            $order = ProductOrderList::getByCode($data['MerchantOrderNo']);

            if ($order == null) {
                return false;
            }
            // 更新會員主訂單狀態 - 已付款
            ProductOrderList::updateById($order[0]['order_id'], ['pay_status' => 1, 'order_status' => 1]);
            $payment = UsersDepositPayment::getPaymentByOrderId($order[0]['order_id']);
            $paymentInfo = $payment[0]['payment_info'];
            $paymentInfo['notice'] = $data;

            $result = DB::transaction(function () use ($payment, $paymentInfo, $order) {
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
     * HashKey 參數加密
     *
     * @param string $parameter
     * @param string $key
     * @param string $iv
     * @return string
     */

    private function create_mpg_aes_encrypt($parameter = "", $key = "", $iv = "")
    {
        $return_str = '';
        if (!empty($parameter)) {
            //將參數經過 URL ENCODED QUERY STRING
            $return_str = http_build_query($parameter);
        }
        return trim(bin2hex(openssl_encrypt(self::addpadding($return_str), 'aes-256-cbc', $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv)));
    }

    private function addpadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    /**
     * HashKey 參數解密
     *
     * @param string $parameter
     * @param string $hash_key
     * @param string $hash_iv
     * @return string
     */

    public function decrypt($parameter = "", $hash_key, $hash_iv)
    {
        $decrypt = $this->stripPadding(openssl_decrypt(hex2bin($parameter),
            'AES-256-CBC', $hash_key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hash_iv));

        return json_decode($decrypt, true);
    }

    private function strippadding($string)
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

    /**
     * HashIV 參數加密
     *
     * @param string $key
     * @param string $tradeinfo
     * @param string $iv
     * @return string
     */

    private function SHA256($key = "", $tradeinfo = "", $iv = "")
    {
        $HashIV_Key = "HashKey=" . $key . "&" . $tradeinfo . "&HashIV=" . $iv;
        return $HashIV_Key;
    }

}
