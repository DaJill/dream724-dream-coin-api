<?php

use Illuminate\Database\Seeder;

class UsersDepositPayment extends Seeder
{
    private $table = 'users_deposit_payment';
    private $db = 'Product';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection($this->db)->table($this->table)->truncate();
        DB::connection($this->db)->table($this->table)->insert([
            [
                'order_id' => 1,
                'payway_id' => 1,
                'deposit_account_id' => 1,
                'payway_info' => '{"no": "1912021932485568", "credit": 35591, "member": "MyI6gxHVm97V1TJipWLs", "expire_at": "2019-12-03 19:32:59", "payment_no": "LLL19336898591"}',
                'deposit_info' => NULL,
                'credit' => 1000,
                'payment_info' => '{"create": {"RtnMsg": "成功", "RtnCode": "1", "SPToken": "51CB8581AACF444F96111445C1889ADA", "MerchantID": "2000132", "MerchantTradeNo": "1CXzKEP5"}, "notice": {"eci": null, "gwsr": null, "staed": null, "stage": null, "stast": null, "RtnMsg": "付款成功", "amount": null, "PayFrom": null, "RtnCode": "1", "StoreID": null, "TradeNo": "1912021932485568", "card4no": null, "card6no": null, "red_dan": null, "red_yet": null, "ATMAccNo": null, "AlipayID": null, "TradeAmt": "999", "ExecTimes": null, "Frequency": null, "PaymentNo": "LLL19336898591", "TradeDate": "2019/12/02 19:32:48", "auth_code": null, "ATMAccBank": null, "MerchantID": "2000132", "PeriodType": null, "red_de_amt": null, "red_ok_amt": null, "PaymentDate": "2019/12/02 19:37:44", "PaymentType": "CVS_CVS", "WebATMAccNo": null, "CustomField1": null, "CustomField2": null, "CustomField3": null, "CustomField4": null, "PeriodAmount": null, "SimulatePaid": "1", "process_date": null, "AlipayTradeNo": null, "CheckMacValue": "D02F587419CF24B3B04C17611283D65D3B3E0CC32F3B4AE37BDDD3BBE018AAAC", "TenpayTradeNo": null, "WebATMAccBank": null, "WebATMBankName": null, "MerchantTradeNo": "1CXzKEP5", "TotalSuccessTimes": null, "TotalSuccessAmount": null, "PaymentTypeChargeFee": "30"}, "receive": {"RtnMsg": "Get CVS Code Succeeded.", "RtnCode": "10100073", "StoreID": null, "TradeNo": "1912021932485568", "Barcode1": null, "Barcode2": null, "Barcode3": null, "TradeAmt": "999", "PaymentNo": "LLL19336898591", "TradeDate": "2019/12/02 19:32:59", "ExpireDate": "2019/12/03 19:32:59", "MerchantID": "2000132", "PaymentType": "CVS_CVS", "CustomField1": null, "CustomField2": null, "CustomField3": null, "CustomField4": null, "MerchantTradeNo": "1CXzKEP5"}}',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'order_id' => 2,
                'payway_id' => 1,
                'deposit_account_id' => 1,
                'payway_info' => '{"no": "1912021932485568", "credit": 35591, "member": "MyI6gxHVm97V1TJipWLs", "expire_at": "2019-12-03 19:32:59", "payment_no": "LLL19336898591"}',
                'deposit_info' => NULL,
                'credit' => 2000,
                'payment_info' => '{"create": {"RtnMsg": "成功", "RtnCode": "1", "SPToken": "51CB8581AACF444F96111445C1889ADA", "MerchantID": "2000132", "MerchantTradeNo": "1CXzKEP5"}, "notice": {"eci": null, "gwsr": null, "staed": null, "stage": null, "stast": null, "RtnMsg": "付款成功", "amount": null, "PayFrom": null, "RtnCode": "1", "StoreID": null, "TradeNo": "1912021932485568", "card4no": null, "card6no": null, "red_dan": null, "red_yet": null, "ATMAccNo": null, "AlipayID": null, "TradeAmt": "999", "ExecTimes": null, "Frequency": null, "PaymentNo": "LLL19336898591", "TradeDate": "2019/12/02 19:32:48", "auth_code": null, "ATMAccBank": null, "MerchantID": "2000132", "PeriodType": null, "red_de_amt": null, "red_ok_amt": null, "PaymentDate": "2019/12/02 19:37:44", "PaymentType": "CVS_CVS", "WebATMAccNo": null, "CustomField1": null, "CustomField2": null, "CustomField3": null, "CustomField4": null, "PeriodAmount": null, "SimulatePaid": "1", "process_date": null, "AlipayTradeNo": null, "CheckMacValue": "D02F587419CF24B3B04C17611283D65D3B3E0CC32F3B4AE37BDDD3BBE018AAAC", "TenpayTradeNo": null, "WebATMAccBank": null, "WebATMBankName": null, "MerchantTradeNo": "1CXzKEP5", "TotalSuccessTimes": null, "TotalSuccessAmount": null, "PaymentTypeChargeFee": "30"}, "receive": {"RtnMsg": "Get CVS Code Succeeded.", "RtnCode": "10100073", "StoreID": null, "TradeNo": "1912021932485568", "Barcode1": null, "Barcode2": null, "Barcode3": null, "TradeAmt": "999", "PaymentNo": "LLL19336898591", "TradeDate": "2019/12/02 19:32:59", "ExpireDate": "2019/12/03 19:32:59", "MerchantID": "2000132", "PaymentType": "CVS_CVS", "CustomField1": null, "CustomField2": null, "CustomField3": null, "CustomField4": null, "MerchantTradeNo": "1CXzKEP5"}}',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'order_id' => 3,
                'payway_id' => 1,
                'deposit_account_id' => 1,
                'payway_info' => '{"no": "1912021932485568", "credit": 35591, "member": "MyI6gxHVm97V1TJipWLs", "expire_at": "2019-12-03 19:32:59", "payment_no": "LLL19336898591"}',
                'deposit_info' => NULL,
                'credit' => 3000,
                'payment_info' => '{"create": {"RtnMsg": "成功", "RtnCode": "1", "SPToken": "51CB8581AACF444F96111445C1889ADA", "MerchantID": "2000132", "MerchantTradeNo": "1CXzKEP5"}, "notice": {"eci": null, "gwsr": null, "staed": null, "stage": null, "stast": null, "RtnMsg": "付款成功", "amount": null, "PayFrom": null, "RtnCode": "1", "StoreID": null, "TradeNo": "1912021932485568", "card4no": null, "card6no": null, "red_dan": null, "red_yet": null, "ATMAccNo": null, "AlipayID": null, "TradeAmt": "999", "ExecTimes": null, "Frequency": null, "PaymentNo": "LLL19336898591", "TradeDate": "2019/12/02 19:32:48", "auth_code": null, "ATMAccBank": null, "MerchantID": "2000132", "PeriodType": null, "red_de_amt": null, "red_ok_amt": null, "PaymentDate": "2019/12/02 19:37:44", "PaymentType": "CVS_CVS", "WebATMAccNo": null, "CustomField1": null, "CustomField2": null, "CustomField3": null, "CustomField4": null, "PeriodAmount": null, "SimulatePaid": "1", "process_date": null, "AlipayTradeNo": null, "CheckMacValue": "D02F587419CF24B3B04C17611283D65D3B3E0CC32F3B4AE37BDDD3BBE018AAAC", "TenpayTradeNo": null, "WebATMAccBank": null, "WebATMBankName": null, "MerchantTradeNo": "1CXzKEP5", "TotalSuccessTimes": null, "TotalSuccessAmount": null, "PaymentTypeChargeFee": "30"}, "receive": {"RtnMsg": "Get CVS Code Succeeded.", "RtnCode": "10100073", "StoreID": null, "TradeNo": "1912021932485568", "Barcode1": null, "Barcode2": null, "Barcode3": null, "TradeAmt": "999", "PaymentNo": "LLL19336898591", "TradeDate": "2019/12/02 19:32:59", "ExpireDate": "2019/12/03 19:32:59", "MerchantID": "2000132", "PaymentType": "CVS_CVS", "CustomField1": null, "CustomField2": null, "CustomField3": null, "CustomField4": null, "MerchantTradeNo": "1CXzKEP5"}}',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'order_id' => 4,
                'payway_id' => 2,
                'deposit_account_id' => 1,
                'payway_info' => '{"no": "1912021934545570", "bank": "土地銀行", "code": "005", "credit": 69146, "member": "MyI6gxHVm97V1TJipWLs", "expire_at": "2019-12-03", "bank_account": "5219833725139022"}',
                'deposit_info' => NULL,
                'credit' => 4000,
                'payment_info' => '{"create": {"RtnMsg": "成功", "RtnCode": "1", "SPToken": "2A4FF29E6AD24DA59CDA12F9EFE1304C", "MerchantID": "2000132", "MerchantTradeNo": "g1v5HI10"}, "notice": {"eci": null, "gwsr": null, "staed": null, "stage": null, "stast": null, "RtnMsg": "付款成功", "amount": null, "PayFrom": null, "RtnCode": "1", "StoreID": null, "TradeNo": "1912021934545570", "card4no": null, "card6no": null, "red_dan": null, "red_yet": null, "ATMAccNo": null, "AlipayID": null, "TradeAmt": "999", "ExecTimes": null, "Frequency": null, "PaymentNo": null, "TradeDate": "2019/12/02 19:34:54", "auth_code": null, "ATMAccBank": null, "MerchantID": "2000132", "PeriodType": null, "red_de_amt": null, "red_ok_amt": null, "PaymentDate": "2019/12/02 19:39:39", "PaymentType": "ATM_LAND", "WebATMAccNo": null, "CustomField1": null, "CustomField2": null, "CustomField3": null, "CustomField4": null, "PeriodAmount": null, "SimulatePaid": "1", "process_date": null, "AlipayTradeNo": null, "CheckMacValue": "14A539A8DF34B4DEC198DBFAEA70E152B973F99FF51CD01E9DA229A7682F1A07", "TenpayTradeNo": null, "WebATMAccBank": null, "WebATMBankName": null, "MerchantTradeNo": "g1v5HI10", "TotalSuccessTimes": null, "TotalSuccessAmount": null, "PaymentTypeChargeFee": "0"}, "receive": {"RtnMsg": "Get VirtualAccount Succeeded", "RtnCode": "2", "StoreID": null, "TradeNo": "1912021934545570", "BankCode": "005", "TradeAmt": "999", "vAccount": "5219833725139022", "TradeDate": "2019/12/02 19:35:05", "ExpireDate": "2019/12/03", "MerchantID": "2000132", "PaymentType": "ATM_LAND", "CustomField1": null, "CustomField2": null, "CustomField3": null, "CustomField4": null, "MerchantTradeNo": "g1v5HI10"}}',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'order_id' => 5,
                'payway_id' => 3,
                'deposit_account_id' => 1,
                'payway_info' => '{"bank": "臺灣銀行", "code": "004", "name": "發大財專戶", "branch": "北屯", "credit": "999", "member": "svlqcq7gZci26ohOC63e", "account": "1688888888"}',
                'deposit_info' => '{"bank": "土地銀行", "code": "005", "name": "王叉", "account": "55688999994"}',
                'credit' => 5000,
                'payment_info' => NULL,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ]);

    }
}
