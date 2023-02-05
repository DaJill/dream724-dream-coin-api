<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductImage extends Model
{
    protected $table = 'product_image';
    protected $connection = 'Product';

    protected $fillable = [
        'id',
        'sort',
        'image_path',
    ];
    
    public $primaryKey = ['id','sort'];
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'id' => 'integer',
        'sort' => 'integer',
        'image_path' => 'string',
    ];

    /**
     * 新增圖片
     *
     * @param [int] $_iProductID 商品ID
     * @param [type] $aImageData 圖片資料 [{商品id}, {排序}, {圖片路徑}] , ....
     *
     * @return object
     */
    public function scopeAdd($_oQuery, $_iProductID, $aImageData)
    {
        $aInsert = [];
        foreach($aImageData as $aRowImage) {
            $aInsert[] = [
                'id' => $_iProductID,
                'sort' => $aRowImage['sort'],
                'image_path' => $aRowImage['image_path'],
            ];
        }
        return self::insert(
            $aInsert
        );
    }

    /**
     * 用商品ID刪除圖片
     *
     * @param [type] $_oQuery
     * @param [type] $_iProductID
     * @return void
     */
    public function scopeDeleteById($_oQuery, $_iProductID)
    {
        return $_oQuery->where('id', $_iProductID)->delete();
    }

    /**
     * replace圖片跟排序
     *
     * @param [type] $_oQuery
     * @param [type] $_iProductID 商品ID
     * @param [type] $aImageData 圖片資料 [{商品id}, {排序}, {圖片路徑}] , ....
     * @return void
     */
    public function scopeReplace($_oQuery, $_iProductID, $_iSort, $_iImagePath)
    {
        return DB::connection($this->connection)->insert(
            'REPLACE INTO '.$this->table.' (id, sort, image_path) VALUES (?, ?, ?);',
            [$_iProductID, $_iSort, $_iImagePath]
        );
    }

    public function scopeGetById($_oQuery, $_iProductID, $_aFields = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        return $_oQuery
        ->where('id', $_iProductID)
        ->orderby('sort')
        ->get($aFields)
        ->toArray();
    }

    public function scopeGetList($_oQuery, $_aFieldsCondition = [], $_aFields = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        foreach($_aFieldsCondition as $sCol=>$mVal) {
            $_oQuery = is_array($mVal) ? $_oQuery->whereIn($sCol, $mVal) : $_oQuery->where($sCol, $mVal);
        }

        return $_oQuery
        ->get($aFields)
        ->toArray();
    }
}
