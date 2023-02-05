<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductList extends Model
{
    protected $table = 'product_list';
    protected $connection = 'Product';

    protected $fillable = [
        'id', //商品ID
        'code',//商品碼
        'name', //名稱
        'description', //介紹
        'price_target', //金額
        'status', //狀態 0下架 1上架
        'end_date',//募款結束時間
        'modify_datetime', //更新時間
    ];

    public $primaryKey = ['id'];
    public $timestamps = false;
    public $incrementing = false; //主鍵自動遞增

    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'name' => 'string',
        'description' => 'string',
        'price_target' => 'integer',
        'status' => 'integer',
        'end_date' => 'datetime:Y-m-d',
        'modify_datetime' => 'datetime',
    ];

    /**
     * 新增商品
     *
     * @param [type] $_oQuery
     * @param string $_sName 名稱
     * @param string $_sDescription 介紹
     * @param integer $_iPrice_Target 目標金額
     * @param integer $_iStatus 狀態 0下架 1上架
     * @param string $_sCode 商品碼
     * @param string $_dEnd 募款結束日
     * @return void
     */
    public function scopeAdd($_oQuery,
    $_sName = '',
    $_sDescription = '',
    $_iPrice_Target = 0,
    $_iStatus = 0,
    $_sCode = '',
    $_dEnd
    )
    {
        return self::insertGetId(
            [
                'name' => $_sName,
                'description' => $_sDescription,
                'price_target' => $_iPrice_Target,
                'status' => $_iStatus,
                'code' => $_sCode,
                'end_date' => $_dEnd
            ]
        );
    }

    /**
     * 利用ID更新
     *
     * @param [object] $_oQuery
     * @param [int] $_iId 商品ID
     * @param [array] $_aUpdateParam 欲更新的資料(參照$this->fillable)
     * @return void
     */
    public function scopeUpdateById($_oQuery, $_iId, $_aUpdateParam)
    {
        $_aUpdateParam['modify_datetime'] = Carbon::now()->toDateTimeString();
        $aUpdate = [];
        foreach($this->fillable as $iParamKey) {
            if(isset($_aUpdateParam[$iParamKey])) {
                $aUpdate[$iParamKey] = $_aUpdateParam[$iParamKey];
            }
        }
        return $_oQuery
        ->where('id', $_iId)
        ->update($aUpdate);
    }

    /**
     * 取得商品列表
     *
     * @param [type] $_oQuery
     * @param array $_aFields 顯示欄位
     * @param array $_aFieldsCondition 欄位條件(只能相等)
     * @param string $_sOrderBy 欲排序的欄位
     * @param string $_sOrderType 排序的類型 asc | desc
     * @param int $_iLimit 頁數
     * @return void
     */
    public function scopeGetList($_oQuery, $_aFields = [], $_aFieldsCondition=[], $_sOrderBy='id', $_sOrderType = 'desc', $_iLimit = 0, $_aDate = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;

        foreach($_aFieldsCondition as $sCol=>$mVal) {
            $_oQuery = is_array($mVal) ? $_oQuery->whereIn($sCol, $mVal) : $_oQuery->where($sCol, $mVal);
        }

        if(!empty($_aDate)) {
            $_oQuery = $_oQuery->whereBetween('modify_datetime', $_aDate);
        }

        $_oQuery = $_oQuery
        ->orderBy($_sOrderBy, $_sOrderType);

        if($_iLimit > 0){//如果有頁數補頁數
            return $_oQuery
            ->select($aFields)
            ->paginate($_iLimit)
            ->appends(['limit' => $_iLimit])
            ->toArray();
        }

        return $_oQuery
        ->get($aFields)
        ->toArray();
    }

    /**
     * 用ID取得商品
     *
     * @param [type] $_oQuery
     * @param [array] $_aProductID 商品id
     * @param array $_aFields 搜尋欄位
     * @return void
     */
    public function scopeGetById($_oQuery, $_aProductID, $_aFields = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        return $_oQuery
        ->whereIn('id', $_aProductID)
        ->get($aFields)
        ->keyBy('id')
        ->toArray();
    }

    public function scopeGetByCode($_oQuery, $_sProductCode, $_aFields = [])
    {
        $aFields = empty($_aFields) ? $this->fillable : $_aFields;
        $aData = $_oQuery
        ->where('code', $_sProductCode)
        ->get($aFields)
        ->toArray();
        if(!empty($aData)) {
            return $aData[0];
        }

        return $aData;
    }
}
