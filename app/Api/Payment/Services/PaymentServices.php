<?php

namespace App\Api\Payment\Services;

use App\Model\Bank\DepositAccounts;
use App\Model\Bank\Banks;
use App\Model\User\Users;
use App\Model\Payment\PaymentSetting;
use App\Model\Payment\UsersDepositPayment;
use App\Api\Log\Traits\LogTraits;
use App\Api\Payment\Services\EcpayServices;
use App\Api\Payment\Services\EbpayServices;
use DB;

class PaymentServices
{
    use LogTraits;

    protected $ecpayServices;
    protected $ebpayServices;

    public function __construct(EcpayServices $ecpayServices,EbpayServices $ebpayServices)
    {
        $this->ecpayServices = $ecpayServices;
        $this->ebpayServices = $ebpayServices;
    }

    /**
     * 存款
     *
     * @param  Request $request [必帶:order_id,code,payway_id,account_id,amount,callback_code,domain 銀行卡:bank_code,bank_name,bank_account]
     * @return mixed
     */
    public function createOrder($request)
    {
        try {
            $result = DB::transaction(function () use ($request) {

                $payment_type = PaymentSetting::getPaymentSetting();

                $result = [];
                switch ($request['payway_id']) {
                    case 1: // 超商代碼
                    case 2: // Web ATM
                        if($payment_type[0]['type'] == 1){
                            $result = $this->ecpayServices->createEcpayOrder($request);
                        }else{
                            $result = $this->ebpayServices->createEbpayOrder($request);
                        }
                        break;
                    case 3: // 銀行卡
                        $user = Users::getUserById($request['account_id']);
                        $bankList = Banks::getBanksCodeIndex();

                        if ($user == null) {
                            return [
                                'result'  => false,
                                'message' => 'user is not exists',
                            ];
                        }

                        // 取得會員要匯款的銀行帳戶
                        $deposit_info = [
                            'code'    => $request['bank_code'], // 銀行代碼
                            'bank'    => $bankList[$request['bank_code']]['name'], // 銀行名稱
                            'account' => $request['bank_account'], // 銀行帳號
                            'name'    => $request['bank_name'], // 戶名
                        ];

                        // 取得要轉帳的公司銀行帳號
                        $bank = DepositAccounts::getEnableDepositAccounts();
                        if ($bank == null) {
                            return [
                                'result'  => false,
                                'message' => 'bankAccount is not exists',
                            ];
                        }

                        $payway_info = [
                            'member'  => $user['account'], // 會員帳號
                            'code'    => $bank['code'], // 銀行代碼
                            'bank'    => $bankList[$bank['code']]['name'] ?? '', // 銀行名稱
                            'branch'  => $bank['branch'], // 分行
                            'account' => $bank['account'], // 銀行帳號
                            'name'    => $bank['name'], // 銀行戶名
                            'credit'  => $request['amount'], // 金額
                        ];
                        // 新增會員存款訂單 - 銀行資訊
                        $deposit = [
                            'order_id'           => $request['order_id'],
                            'payway_id'          => $request['payway_id'],
                            'deposit_account_id' => $bank['id'],
                            'credit'             => $request['amount'],
                            'deposit_info'       => $deposit_info,
                            'payway_info'        => $payway_info,
                        ];
                        $result['result'] = true;
                        $result['data'] = UsersDepositPayment::create($deposit);
                        break;
                    default:
                }
                return $result;
            });

            return $result;
        } catch (\Exception $e) {
            $error = ['request' => $request,'error' => $e->getMessage()];
            $this->createLog('PaymentCreate',$error);
            return [
                'result'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
