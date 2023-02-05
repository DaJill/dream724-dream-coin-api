<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CodeError\Product\ProductException;
use Illuminate\Support\Facades\Storage;
use App\Model\Product\ProductImage;
use App\Model\Product\ProductList;
use App\Model\Product\FundingType;
use App\Model\Product\ProductOrderList;
use Validator;

use Hash;

class Product extends Controller
{
    //圖片限制
    private $aImgLimit = [
        'scale' => [1,5], //比例(小的要在前面)
        'count' => 9, //數量
        'size' => 5000000, //檔案大小5mb
        'type' => ['image/jpeg', 'image/jpg', 'image/png'], //檔案格式
        'name_len' => 450,
    ];
    
    private $aFundingTypeKey = [
        'description' => 'required|string', //內容摘要
        'price' => 'required|integer', //回饋金額
        'delivery_date' => 'required|date_format:Y-m-d', //預計出貨日
    ];

    /**
     * 商品下架
     *
     * @param Request $_oRequest
     * @return ['result' => true, 'data' => []]
     */
    public function stopProduct($_iProductID, ProductList $_oProductList)
    {
        $_oProductList::updateById($_iProductID, ['status' => 0]);
        return ['result' => true, 'data' => []];
    }

    /**
     * 更新商品
     *
     * @param Request $_oRequest
     * @param [type] $_iProductID
     * @return void
     */
    public function updateProduct(Request $_oRequest, ProductList $_oProductList, ProductImage $_oProductImage, FundingType $_oFundingType, $_iProductID)
    {
        $aRequest = $_oRequest->all();
        $aImage = $_oRequest->file('img');//圖片
        $bHasImage = $_oRequest->hasFile('img'); //有圖片true
        $bHasFundingType = isset($aRequest['funding_type']);
        
        //檢查募款設定
        if($bHasFundingType){
            $this->checkFundingTypeKey($aRequest['funding_type']);
        }

        //檢查圖片
        if($bHasImage){
            $this->checkImage($aImage);
        }

        if (isset($aRequest['date'])) {
            $aRequest['end_date'] = $aRequest['date'];
            unset($aRequest['date']);
        }

        //更新商品資訊
        if(!$_oProductList::updateById($_iProductID, $aRequest)){
            throw new ProductException('PRODUCT_LIST_UPDATE_ERROR');
        }

        //募款設定刪除並重新新增
        if ($bHasFundingType) {
            $aFundingType = $aRequest['funding_type'];
            $iSort = 0;
            foreach($aFundingType as $iKey => $aRowData) {
                $aFundingType[$iKey]['sort'] = $iSort;
                $iSort++;
            }
            $_oFundingType::deleteById($_iProductID);
            $_oFundingType::add($_iProductID, $aFundingType);
        }

        //圖片刪除並重新新增
        if ($bHasImage) {
            $aImg = $_oProductImage::getById($_iProductID, ['image_path']);
            foreach($aImg as $sFilePath) {
                Storage::delete($sFilePath);
            }
            $_oProductImage::deleteById($_iProductID);
            $this->uploadImage($_iProductID, $aImage);
        }

        return ['result' => true, 'data' => []];
    }

    /**
     * 取得商品列表
     *
     * @param Request $_oRequest
     * [
     *  fields=>[id, description, name, ...],
     *  fields_condition=>[status => 0, ...],
     *  order_by=>id,
     *  order_type=>asc,
     *  limit=>20
     * ]
     * @return ['result' => true, 'data' => {資料}, 'total' => {總筆數}]
     */
    public function getProductListByMem(
        Request $_oRequest, 
        ProductList $_oProductList, 
        ProductOrderList $_oProductOrderList,
        ProductImage $_oProductImage
    )
    {
        $aRequest = [];
        $aRequest['name'] = null; //商品名稱
        $aRequest['code'] = null; //商品碼
        $aRequest['product_id'] = null; //商品ID
        $aRequest['status'] = 1; //商品 1上架 0下架
        $aRequest['start_date'] = $_oRequest->input('start_date', null); //更新開始時間
        $aRequest['end_date'] = $_oRequest->input('end_date', null); //更新結束時間
        $aRequest['order_by'] = $_oRequest->input('order_by', 'id'); //排序欄位
        $aRequest['order_type'] = $_oRequest->input('order_type', 'desc'); //排序方式 升序或降序
        $aRequest['limit'] = $_oRequest->input('limit', 20); //一頁筆數
        $aFields=[
            'code',
            'name',
            'description',
            'price_target',
            'end_date',
            'raised',
            'image_path',
        ];
        $aData = $this->getProductList($aRequest, $_oProductList, $_oProductOrderList, $_oProductImage, $aFields);
        
        return ['result' => true, 'data' => $aData];
    }

    /**
     * 取得商品列表V2
     *
     * @param Request $_oRequest
     * @return ['result' => true, 'data' => {資料}, 'total' => {總筆數}]
     */
    public function getProductListByAdmin(
        Request $_oRequest, 
        ProductList $_oProductList, 
        ProductOrderList $_oProductOrderList,
        ProductImage $_oProductImage
    )
    {
        $aRequest = [];
        $aRequest['name'] = $_oRequest->input('name', null); //商品名稱
        $aRequest['code'] = $_oRequest->input('code', null); //商品碼
        $aRequest['product_id'] = $_oRequest->input('product_id', null); //商品ID
        $aRequest['status'] = $_oRequest->input('status', null); //商品 上架下架
        $aRequest['start_date'] = $_oRequest->input('start_date', null); //更新開始時間
        $aRequest['end_date'] = $_oRequest->input('end_date', null); //更新結束時間
        $aRequest['order_by'] = $_oRequest->input('order_by', 'id'); //排序欄位
        $aRequest['order_type'] = $_oRequest->input('order_type', 'desc'); //排序方式 升序或降序
        $aRequest['limit'] = $_oRequest->input('limit', 20); //一頁筆數


        $aData = $this->getProductList($aRequest, $_oProductList, $_oProductOrderList, $_oProductImage);
        return ['result' => true, 'data' => $aData];
    }

    private function getProductList($_aRequest, $_oProductList, $_oProductOrderList, $_oProductImage, $_aFields = [])
    {
        $sName = $_aRequest['name']; //商品名稱
        $sCode = $_aRequest['code']; //商品碼
        $sID = $_aRequest['product_id']; //商品ID
        $iStatus = $_aRequest['status']; //商品 上架下架
        $dStart = $_aRequest['start_date']; //更新開始時間
        $dEnd = $_aRequest['end_date']; //更新結束時間
        $sOrderBy = $_aRequest['order_by']; //排序欄位
        $sOrderType = $_aRequest['order_type']; //排序方式 升序或降序
        $iLimit = $_aRequest['limit']; //一頁筆數

        $aFieldsConditionTmp = [
            'name' => $sName,
            'code' => $sCode,
            'id' => $sID,
            'name' => $sName,
            'status' => $iStatus,
        ];
        $aFieldsCondition = [];

        foreach($aFieldsConditionTmp as $sCol => $sVal) {
            if($sVal === null) {
                continue;
            }
            $aFieldsCondition[$sCol] = $sVal;
        }

        $aDate = [];
        if($dStart !== null) {
            if($dEnd === null) { //開始跟結束日缺一不可
                throw new ProductException('PRODUCT_SEARCH_DATE_TIME_ERROR');
            }

            $aDate = [$dStart, $dEnd];
        }
        $aData = $_oProductList::getList([], $aFieldsCondition, $sOrderBy, $sOrderType, $iLimit, $aDate);
        $aProductID = collect($aData['data'])->pluck('id')->toArray();
        
        $aRaised = $_oProductOrderList::getRaisedByProductId($aProductID);
        $aRaised = collect($aRaised)->pluck('raised', 'product_id')->all();
        $aImg = $_oProductImage::getList(['id' => $aProductID, 'sort' => 0], ['id', 'image_path']);
        $aImg = collect($aImg)->pluck('image_path', 'id')->all();
        foreach($aProductID as $iProductID) {
            if(!isset($aRaised[$iProductID])) {
                $aRaised[$iProductID] = 0;
            }
            if(!isset($aImg[$iProductID])) {
                $aImg[$iProductID] = "";
            }
        }
        foreach($aData['data'] as $iKey => $aRowData) {
            $iProductID = $aRowData['id'];
            $aRowData['raised'] = (int)$aRaised[$iProductID];
            $aRowData['image_path'] = $aImg[$iProductID];
            $aData['data'][$iKey] = $aRowData;
            if(empty($_aFields)) {
                $aData['data'][$iKey] = $aRowData;
            }else {
                $aData['data'][$iKey] = collect($aRowData)->only($_aFields);
            }
        }
        return $aData;
    }

    public function getProductDetailByAdmin(
        $_iProductID, 
        ProductList $_oProductList, 
        FundingType $_oFundingType, 
        ProductImage $_oProductImage,
        ProductOrderList $_oProductOrderList
    )
    {
        $aData = $this->getProductDetail([], ['id'=>$_iProductID], $_oProductList, $_oFundingType, $_oProductImage, $_oProductOrderList);
        return ['result' => true, 'data' => $aData];
    }

    public function getProductDetailByMem(
        $_sProductCode, 
        ProductList $_oProductList, 
        FundingType $_oFundingType, 
        ProductImage $_oProductImage,
        ProductOrderList $_oProductOrderList
    )
    {
        $aFields = [
            'code',
            'name',
            'description',
            'price_target',
            'raised',
            'raised_people',
            'end_date',
            'funding_type',
            'img',
        ];
        $aData = $this->getProductDetail($aFields, ['code'=>$_sProductCode, 'status'=>1], $_oProductList, $_oFundingType, $_oProductImage, $_oProductOrderList);
        return ['result' => true, 'data' => $aData];
    }
    /**
     * 用商品ID 取得詳細資訊
     *
     * @param [type] $_iProductID
     * @return "result": true
     */
    private function getProductDetail(
        $_aFields, 
        $_aFieldsCondition,
        $_oProductList, 
        $_oFundingType, 
        $_oProductImage,
        $_oProductOrderList
    )
    {
        $aData = $_oProductList::getList([], $_aFieldsCondition);
        if(empty($aData)) {
            throw new ProductException('PRODUCT_DETAIL_DOES_NOT_EXIST');
        }

        $aData = $aData[0];
        $iProductID = $aData['id'];
        
        //累積金額與人數
        $aRaised = $_oProductOrderList::getRaisedByProductId([$iProductID]);
        $aData['raised'] = 0;
        $aData['raised_people'] = 0;
        if(!empty($aRaised)) {
            $aRaised = $aRaised[0];
            $aData['raised'] = $aRaised['raised'];
            $aData['raised_people'] = $aRaised['raised_people'];
        }

        $aData['funding_type'] = $_oFundingType::getById($iProductID, ['price', 'description', 'delivery_date']);

        $aData['img'] = [];
        $aImg = $_oProductImage::getById($iProductID, ['image_path']);
        foreach($aImg as $aRow) {
            $aData['img'][] = $aRow['image_path'];
        }

        if(!empty($_aFields)) {
            $aData = collect($aData)->only($_aFields);
        }

        return $aData;
    }

    /**
     * 新增商品
     *
     * @param Request $_oRequest
     * @return ['result' => true, 'data' => []]
     */
    public function addProduct(Request $_oRequest, ProductList $_oProductList, FundingType $_oFundingType)
    {
        $sName = $_oRequest->input('name', ''); //專案標題
        $sDescription = $_oRequest->input('description', ''); //專案描述
        $iPriceTarget = $_oRequest->input('price_target', 0); //募資目標金額
        $iStatus = $_oRequest->input('status', 1); //狀態 0下架, 1上架
        $dEndDate = $_oRequest->input('date'); //募款結束時間
        $aFundingType = $_oRequest->input('funding_type'); //募款設定
        $aImage = $_oRequest->file('img');//圖片
        $bHasImage = $_oRequest->hasFile('img'); //有圖片true
        
        //檢查圖片
        if($bHasImage){
            $this->checkImage($aImage);
        }

        //檢查募款設定
        $this->checkFundingTypeKey($aFundingType);

        //商品碼
        $sCode = Hash::make(time());
        $sCode = str_replace('/', '', $sCode); //去掉斜線，不然客端連結會有問題
        $sCode = substr($sCode, -8);

        //取得商品ID
        $iProductID = $_oProductList::add($sName, $sDescription, $iPriceTarget, $iStatus, $sCode, $dEndDate);

        $iSort = 0;
        foreach($aFundingType as $iKey => $aRowData) {
            $aFundingType[$iKey]['sort'] = $iSort;
            $iSort++;
        }
        //新增回饋設定
        $_oFundingType::add($iProductID, $aFundingType);
        
        //上傳圖片
        if ($bHasImage) {
            $this->uploadImage($iProductID, $aImage);
        }

        return ['result' => true, 'data' => []];
    }

    /**
     * 上傳圖片並記錄路徑
     *
     * @param [array] $_aImg 欲上傳的圖片
     * @param [int] $_iProductID 商品ID
     * @return void
     */
    private function uploadImage($_iProductID, $_aImg)
    {
        $aImg = array_values($_aImg); //重整圖片index, 因為index跟排序有關
        $aInsertDBImg = [];
        foreach($aImg as $iSort => $oRowImg) {
            $sName = time().$iSort.$oRowImg->getClientOriginalName();
            $sFilePath = 'product_images/'.$sName;
            
            //上傳圖片
            Storage::disk('s3')->put($sFilePath, file_get_contents($oRowImg), 'public');
    
            $aInsertDBImg[] = [
                'sort' => $iSort,
                'image_path' => $sFilePath
            ];
        }

        ProductImage::add($_iProductID, $aInsertDBImg);
        
        return true;
    }

    private function checkFundingTypeKey($_aFundingType)
    {
        foreach($_aFundingType as $aRowData) {
            if(!is_array($aRowData)) { //不是二維陣列資料就錯誤
                throw new ProductException('PRODUCT_FUNDING_TYPE_ERROR');
            }

            if(Validator::make($aRowData, $this->aFundingTypeKey)->fails()) {
                throw new ProductException('PRODUCT_FUNDING_TYPE2_ERROR');
            }
        }
        return true;
    }
    /**
     * 驗證圖片格式是否正確
     *
     * @param [array] $_aImg 圖片file的array
     * @return [bool] true
     */
    private function checkImage($_aImg)
    {
        //驗證數量
        if(count($_aImg) > $this->aImgLimit['count']){
            throw new ProductException('PRODUCT_IMG_COUNT_OVER');
        }
    
        $fScaleLimit = $this->aImgLimit['scale'][1]/$this->aImgLimit['scale'][0]; //比例限制
    
        foreach($_aImg as $oRowImg) {
            //驗證容量
            if($oRowImg->getSize() > $this->aImgLimit['size']) {
                throw new ProductException('PRODUCT_IMG_SIZE_OVER');
            }
            
            //驗證圖片種類
            if(!in_array($oRowImg->getMimeType(), $this->aImgLimit['type'])) {
                throw new ProductException('PRODUCT_IMG_MIME_TYPE_ERROR');
            }
    
            //驗證圖片名稱長度
            if(mb_strlen($oRowImg->getClientOriginalName()) > $this->aImgLimit['name_len']) {
                throw new ProductException('PRODUCT_NAME_LEN_OVER');
            }
    
            $aWH = getimagesize($oRowImg);
            //驗證比例
            $iLong = $aWH[0];
            $iSort = $aWH[1];
            if($iLong < $iSort){ //寬比較短要互換
                $iTmpLong = $iSort;
                $iSort = $iLong;
                $iLong = $iTmpLong;
            }

            if($fScaleLimit < ($iLong / $iSort)) { //比例錯
                throw new ProductException('PRODUCT_IMG_SCALE_ERROR');
            }
        }

        return true;
    }

}
