<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class FundingType extends Model
{
    protected $table = 'funding_type';
    protected $connection = 'Product';

    protected $fillable = [
        'id', //商品ID
        'sort',//排序
        'description', //內容摘要
        'price', //回饋金額
        'delivery_date', //預計出貨日
    ];
    
    public $primaryKey = ['id', 'sort'];
    public $timestamps = false;
    public $incrementing = false; //主鍵自動遞增

    protected $casts = [
        'id' => 'integer',
        'sort' => 'integer',
        'description' => 'string',
        'price' => 'integer',
        'delivery_date' => 'datetime:Y-m-d',
    ];

    /**
     * 新增回饋設定
     *
     * @param [int] $_iProductID 專案ID
     * @param [type] $_aFundingData 回饋設定資料 [
     *  ['sort'=>0, 'description'=>'asdqwe', 'price'=>500, 'delivery_date'=>'2010-10-10']
     *  ,....
     * ]
     *
     * @return object
     */
    public function scopeAdd($_oQuery, $_iProductID, $_aFundingData)
    {
        $aInsert = [];
        foreach($_aFundingData as $aRowData) {
            $aInsert[] = [
                'id' => $_iProductID,
                'sort' => $aRowData['sort'],
                'description' => $aRowData['description'],
                'price' => $aRowData['price'],
                'delivery_date' => $aRowData['delivery_date'],
            ];
        }
        return self::insert(
            $aInsert
        );
    }

    /**
     * 用專案ID 刪除回饋設定
     *
     * @param [type] $_oQuery
     * @param [type] $_iProductID 商品ID
     * @return void
     */
    public function scopeDeleteById($_oQuery, $_iProductID)
    {
        return $_oQuery->where('id', $_iProductID)->delete();
    }

    /**
     * 用商品ID 取得回饋設定
     *
     * @param [type] $_oQuery
     * @param [type] $_iProductID
     * @param array $_aFields
     * @return void
     */
    public function scopeGetById($_oQuery, $_iProductID, $_aFields = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        return $_oQuery
        ->where('id', $_iProductID)
        ->orderby('sort')
        ->get($aFields)
        ->toArray();
    }
}
