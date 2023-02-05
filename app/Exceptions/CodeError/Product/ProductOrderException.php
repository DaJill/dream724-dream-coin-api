<?php
namespace App\Exceptions\CodeError\Product;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\Product\ProductOrder;

class ProductOrderException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const path = ProductOrder::class;

    /**
     * 代碼表.
     */
    const codeMap = [
        'ORDER_PRODUCT_IS_NOT_EXIST' => ['001', 'order is error', '訂貨商品不存在'],
        'ORDER_PRODUCT_IS_STOP' => ['002', 'order is error', '訂貨商品被停貨'],
        'ORDER_PRODUCT_IS_NOT_ENOUGH' => ['003', 'order is error', '訂貨商品不足夠'],
        'ORDER_PRODUCT_PURCHASE_ERROR' => ['004', 'order is error', '訂貨商品數量錯誤'],
        'ORDER_PRODUCT_SEARCH_DATE_TIME_ERROR' => ['005', 'search date time is error', '搜尋日期錯誤'],
        'ORDER_PRODUCT_UPDATE_NOT_FOUND_ADMIN' => ['006', 'update order is error', '找不到操作更新的管理者'],
        'ORDER_PRODUCT_BANK_ERROR' => ['007', 'order is error', '銀行資訊不完整'],
        'ORDER_PRODUCT_PAYMENT_ERROR' => ['008', 'order is error', '銀行資訊無預期錯誤'],
        'ORDER_PRODUCT_PAYMENT_USER_NOT_EXIST' => ['009', 'order is error', '付款檢查沒有此會員'],
        'ORDER_PRODUCT_BANK_ACCOUNT_NOT_EXIST' => ['010', 'order is error', '公司銀行尚未建立存款帳戶'],
        'ORDER_PRODUCT_ECPAY_ERROR' => ['011', 'order is error', '綠界產生訂單錯誤'],
        'ORDER_PRODUCT_STATUS_ERROR' => ['012', 'order is error', '商品已被下架'],
    ];

    public function __construct($errorKey, array $data = [])
    {
        $message = self::codeMap[$errorKey][1];

        if (!empty($data)) {
            $message .= '@'.json_encode($data);
        }
        parent::__construct($message, parent::mainCodeMap[__CLASS__].self::codeMap[$errorKey][0]);
    }
}
