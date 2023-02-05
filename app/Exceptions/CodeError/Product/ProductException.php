<?php
namespace App\Exceptions\CodeError\Product;

use App\Exceptions\CodeError\CodeErrorException;
use App\Http\Controllers\Product\Product;

class ProductException extends CodeErrorException
{
    /**
     * 程式路徑.
     */
    const path = Product::class;

    /**
     * 代碼表.
     */
    const codeMap = [
        'PRODUCT_IS_EXIST' => ['001', 'account is exist', '商品已經存在'],
        'PRODUCT_IMG_COUNT_OVER' => ['002', 'img upload error', '商品圖片超過上限'],
        'PRODUCT_IMG_MIME_TYPE_ERROR' => ['003', 'img upload error', '商品圖片格式錯誤'],
        'PRODUCT_IMG_SIZE_OVER' => ['004', 'img upload error', '商品圖片大小(容量)超過上限'],
        'PRODUCT_IMG_SCALE_ERROR' => ['005', 'img upload error', '商品圖片比例錯誤'],
        'PRODUCT_PAYMENT_TYPE_ERROR' => ['006', 'product upload error', '付款方式格式錯誤'],
        'PRODUCT_NAME_LEN_OVER' => ['007', 'product upload error', '商品字串長度超過'],
        'PRODUCT_DELIVERY_TYPE_ERROR' => ['008', 'product upload error', '寄送方式格式錯誤'],
        'PRODUCT_LIST_UPDATE_ERROR' => ['009', 'product upload error', '無此商品可更新'],
        'PRODUCT_SEARCH_DATE_TIME_ERROR' => ['010', 'product search error', '商品日期輸入錯誤'],
        'PRODUCT_FUNDING_TYPE_ERROR' => ['011', 'product upload error', '回饋設定格式錯誤'],
        'PRODUCT_FUNDING_TYPE2_ERROR' => ['012', 'product upload error', '回饋設定格式錯誤'],
        'PRODUCT_DETAIL_DOES_NOT_EXIST' => ['013', 'product get error', '取得單一商品詳細資訊不存在'],
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
