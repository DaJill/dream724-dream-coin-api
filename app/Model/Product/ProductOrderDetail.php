<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;
use DB;
class ProductOrderDetail extends Model
{
    protected $table = 'product_order_detail';
    protected $connection = 'Product';

    protected $fillable = [
        'detail_id', //細單ID
        'order_id', //訂單ID
        'product_id', //商品ID
        'name', //商品名稱
        'price', //商品單價
        'count', //商品數量
    ];
    
    public $primaryKey = ['detail_id'];
    public $timestamps = false;
    public $incrementing = false; //主鍵自動遞增

    protected $casts = [
        'detail_id' => 'integer',
        'order_id' => 'integer',
        'product_id' => 'integer',
        'name' => 'string',
        'price' => 'integer',
        'count' => 'integer',
    ];

    /**
     * 新增訂單細單
     *
     * @param [type] $_oQuery
     * @param [type] $_aInsert
     * @return void
     */
    public function scopeAdd($_oQuery, $_aInsert)
    {
        $aInsert = [];
        foreach($_aInsert as $iKey => $aRow) { //驗證參數欄位
            foreach($aRow as $sCol => $oVal) {
                if(in_array($sCol, $this->fillable)) {
                    $aInsert[$iKey][$sCol] = $oVal;
                }
            }
        }
        return self::insert(
            $aInsert
        );
    }

    public function scopeGetById($_oQuery, $_iOrderId)
    {
        return $_oQuery
        ->select(DB::raw('product_id, name, price, count, (price * count) as total_price'))
        ->where('order_id', $_iOrderId)
        ->orderBy('detail_id')
        ->get()->toArray();
    }
}
