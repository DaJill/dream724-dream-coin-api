<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Product\ProductOrderList;
use App\Model\Product\ProductList;
use App\Model\Product\ProductOrderDetail;
use App\Exceptions\CodeError\Product\ProductOrderException;
use App\Model\User\Users;
use App\Model\Admin\AdminUser;
use App\Model\Payment\UsersDepositPayment;
use App\Model\Payment\PaymentSetting;
use App\Api\Payment\Services\PaymentServices;
use Hash;
use Carbon\Carbon;

class ProductOrder extends Controller
{
    public function getOrderDetail($_iOrderID, ProductOrderDetail $_oProductOrderDetail, ProductOrderList $_oProductOrderList)
    {
        $aDetail = $_oProductOrderDetail::getById($_iOrderID);
        $aOrder = $_oProductOrderList::getById($_iOrderID, ['order_id', 'code', 'price_total', 'name', 'mobile', 'payway_id', 'address']);

        return [
            'result' => true,
            'data' =>
            [
                'order' => $aOrder,
                'detail' => $aDetail,
            ]
        ];
    }

    public function getOrderListByMem(
        Request $_oRequest,
        ProductOrderList $_oProductOrderList,
        Users $_oUsers,
        AdminUser $_oAdminUser,
        ProductList $_oProductList

    ){
        $aRequest['order_status'] = $_oRequest->input('order_status', null); //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $aRequest['delivery_status'] = $_oRequest->input('delivery_status', null); //出貨狀態 0待出貨, 1已出貨, -1出貨取消
        $aRequest['start_date'] = $_oRequest->input('start_date'); //開始時間
        $aRequest['end_date'] = $_oRequest->input('end_date'); //結束時間
        $aRequest['limit'] = $_oRequest->input('limit', 20); //每頁筆數
        $aRequest['account_id'] = auth()->user()->id;
        $aRequest['account'] = null;
        $aRequest['order_id'] = null;
        $aRequest['mobile'] = null;
        $aRequest['payway_id'] = null;
        $aRequest['code'] = null; //訂單碼
        $aRequest['callback_code'] = null; //廠商回傳代碼
        $aRequest['order_by'] = $_oRequest->input('order_by', 'order_id'); //排序欄位
        $aRequest['order_type'] = $_oRequest->input('order_type', 'desc'); //排序方式 升序或降序
        $aFields = [
            'name',
            'payway_id',
            'mobile',
            'address',
            'order_datetime',
            'deadline',
            'pay_status',
            'order_status',
            'delivery_status',
            'price_total',
            'reason',
            'note',
            'product_code',
            'product_name',
            'product_end_date',
            'product_in_fund',
        ];

        $aData = $this->getOrderList($aRequest, $_oProductOrderList, $_oUsers, $_oAdminUser, $_oProductList, $aFields);
        return ['result' => true, 'data' => $aData];
    }
    /**
     *  用訂單狀態來搜訂單
     *
     * @param Request $_oRequest
     * @param ProductOrderList $_oProductOrderList
     * @return void
     */
    public function getOrderListByAdmin(
        Request $_oRequest,
        ProductOrderList $_oProductOrderList,
        Users $_oUsers,
        AdminUser $_oAdminUser,
        ProductList $_oProductList
    )
    {
        $aRequest['code'] = $_oRequest->input('code', null); //訂單碼
        $aRequest['order_id'] = $_oRequest->input('order_id', null); //訂單id
        $aRequest['mobile'] = $_oRequest->input('mobile', null); //手機號碼
        $aRequest['payway_id'] = $_oRequest->input('payway_id', null); //付款類型
        $aRequest['account_id'] = $_oRequest->input('account_id', null); //帳號id
        $aRequest['account'] = $_oRequest->input('account', null); //帳號名稱
        $aRequest['order_status'] = $_oRequest->input('order_status', null); //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $aRequest['delivery_status'] = $_oRequest->input('delivery_status', null); //出貨狀態 0待出貨, 1已出貨, -1出貨取消
        $aRequest['callback_code'] = $_oRequest->input('callback_code', null); //廠商回傳代碼
        $aRequest['order_by'] = $_oRequest->input('order_by', 'modify_datetime'); //排序欄位
        $aRequest['order_type'] = $_oRequest->input('order_type', 'desc'); //排序方式 升序或降序
        $aRequest['start_date'] = $_oRequest->input('start_date'); //開始時間
        $aRequest['end_date'] = $_oRequest->input('end_date'); //結束時間
        $aRequest['limit'] = $_oRequest->input('limit', 20); //每頁筆數
        $aData = $this->getOrderList($aRequest, $_oProductOrderList, $_oUsers, $_oAdminUser, $_oProductList);
        return ['result' => true, 'data' => $aData];
    }

    private function getOrderList(
        $_aRequest,
        $_oProductOrderList,
        $_oUsers,
        $_oAdminUser,
        $_oProductList,
        $_aFields = []
    )
    {
        $sCode = $_aRequest['code']; //訂單碼
        $iId = $_aRequest['order_id']; //訂單id
        $sMobile = $_aRequest['mobile']; //手機號碼
        $iPaywayId = $_aRequest['payway_id']; //付款類型
        $iAccountId = $_aRequest['account_id']; //帳號id
        $sAccount = $_aRequest['account']; //帳號名稱
        $iOrderStatus = $_aRequest['order_status']; //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $iDeliveryStatus = $_aRequest['delivery_status']; //出貨狀態 0待出貨, 1已出貨, -1出貨取消
        $iCallbackCode = $_aRequest['callback_code']; //廠商回傳代碼
        $sOrderBy = $_aRequest['order_by']; //排序欄位
        $sOrderType = $_aRequest['order_type']; //排序方式 升序或降序
        $dStart =  $_aRequest['start_date']; //開始時間
        $dEnd =  $_aRequest['end_date']; //結束時間
        $iLimit =  $_aRequest['limit']; //每頁筆數
        $aDate = [];

        $aFieldsConditionTmp = [
            'order_status' => $iOrderStatus,
            'delivery_status' => $iDeliveryStatus,
            'code' => $sCode,
            'order_id' => $iId,
            'mobile' => $sMobile,
            'payway_id' => $iPaywayId,
            'account_id' => $iAccountId,
            'account' => $sAccount,
            'callback_code' => $iCallbackCode,
        ];

        $aFieldsCondition = [];
        foreach($aFieldsConditionTmp as $sKey => $sVal){
            if($sVal === null){
                continue;
            }
            $aFieldsCondition[$sKey] = $sVal;
        }

        if($dStart !== null) {
            if($dEnd === null) { //開始跟結束日缺一不可
                throw new ProductOrderException('ORDER_PRODUCT_SEARCH_DATE_TIME_ERROR');
            }
            $aDate = [$dStart, $dEnd];
        }
        $aData = $_oProductOrderList::getList($aFieldsCondition, [], $aDate, $iLimit);
        if($iLimit == 0) {
            $aDataTmp['data'] = $aData;
            $aData = $aDataTmp;
        }
        $aUserID = collect($aData['data'])->pluck('account_id','account_id')->all();
        $aAdminID = collect($aData['data'])->pluck('modify_admin_id','modify_admin_id')->all();
        $aProductID = collect($aData['data'])->pluck('product_id', 'product_id')->all();
        $aUsers = collect($_oUsers::getList(['id', 'account'], ['id' => $aUserID]))->pluck('account', 'id')->all();
        $aAdmins = collect($_oAdminUser::getList(['id', 'account'], ['id' => $aAdminID]))->pluck('account', 'id')->all();
        $aProductCodes = collect($_oProductList::GetList(['id', 'code', 'name', 'end_date'], ['id' => $aProductID]))->keyBy('id')->all();

        $iNow = strtotime(Carbon::now()->toDateString());

        //將user, admin, product_code塞進去
        foreach($aData['data'] as $iKey => $aRowOrder){
            if(isset($aRowOrder['user'])){
                unset($aRowOrder['user']);
            }
            $aProduct = $aProductCodes[$aRowOrder['product_id']];
            $aRowOrder['account'] = $aUsers[$aRowOrder['account_id']];
            $aRowOrder['product_code'] = $aProduct['code'];
            $aRowOrder['product_name'] = $aProduct['name'];
            $aRowOrder['product_end_date'] = $aProduct['end_date'];
            $aRowOrder['product_in_fund'] = (strtotime($aProduct['end_date']) >= $iNow) ? true : false;

            if($aRowOrder['modify_admin_id'] != null){
                $aRowOrder['modify_admin_account'] = $aAdmins[$aRowOrder['modify_admin_id']];
            }

            if(empty($_aFields)) {
                $aData['data'][$iKey] = $aRowOrder;
            }else {
                $aData['data'][$iKey] = collect($aRowOrder)->only($_aFields);
            }
        }

        if($sOrderType == 'asc') {
            $aData['data'] = collect($aData['data'])->sortBy($sOrderBy)->all();
        }else if($sOrderType == 'desc') {
            $aData['data'] = collect($aData['data'])->sortByDesc($sOrderBy)->all();
        }
        $aData['data'] = array_values($aData['data']);
        return $aData;
    }

    /**
     * 會員端使用
     *
     * @param Request $_oRequest
     * @param ProductList $_oProductList
     * @param ProductOrderList $_oProductOrderList
     * @param ProductOrderDetail $_oProductOrderDetail
     * @param PaymentServices $_oPaymentServices
     * @param [type] $_sProductCode
     * @return void
     */
    public function addOrderMem(
        Request $_oRequest,
        ProductList $_oProductList,
        ProductOrderList $_oProductOrderList,
        ProductOrderDetail $_oProductOrderDetail,
        PaymentServices $_oPaymentServices,
        PaymentSetting $_oPaymentSetting,
        $_sProductCode
    )
    {
        $aUser = auth()->user()->toArray();
        $aRequest['account_id'] = $aUser['id']; //會員ID
        $aRequest['name'] = $_oRequest->input('name'); //寄信人名稱
        $aRequest['mobile'] = $_oRequest->input('mobile', $aUser['mobile']); //手機
        $aRequest['payway_id'] = $_oRequest->input('payway_id'); //交易付款ID 1:第三方超商付款, 2:第三方ATM付款, 3:銀行
        $aRequest['address'] = $_oRequest->input('address'); //地址
        $aRequest['deadline_days'] = 1; //付款截止天數
        $aRequest['pay_status'] = 0; //付款狀態 0未付款, 1已付款
        $aRequest['order_status'] = 0; //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $aRequest['delivery_status'] = 0; //出貨狀態 0待出貨, 1已出貨, -1出貨取消
        $aRequest['price'] = $_oRequest->input('price'); //募款金額
        $aRequest['bank_code'] = $_oRequest->input('bank_code', null); //銀行代碼
        $aRequest['bank_name'] = $_oRequest->input('bank_name', null); //銀行戶名
        $aRequest['bank_account'] = $_oRequest->input('bank_account', null); //銀行帳號
        $aRequest['product_code'] = $_sProductCode;
        $aRequest['note'] = $_oRequest->input('note', null); //會員備註
        $aRequest['callback_code'] = null; //廠商回傳代碼
        $aRequest['domain'] = ''; //domain
        $aPayment = $this->createOrderPayment(
            $aRequest,
            $_oProductList,
            $_oProductOrderList,
            $_oProductOrderDetail,
            $_oPaymentServices,
            $_oPaymentSetting
        );
        return ['result' => true, 'data' => $aPayment['data']];
    }

    /**
     * xin會員端使用
     *
     * @param Request $_oRequest
     * @param ProductList $_oProductList
     * @param ProductOrderList $_oProductOrderList
     * @param ProductOrderDetail $_oProductOrderDetail
     * @param PaymentServices $_oPaymentServices
     * @param [type] $_sProductCode
     * @return void
     */
    public function addOrder(
        Request $_oRequest,
        ProductList $_oProductList,
        ProductOrderList $_oProductOrderList,
        ProductOrderDetail $_oProductOrderDetail,
        PaymentServices $_oPaymentServices,
        PaymentSetting $_oPaymentSetting,
        Users $_oUsers
    )
    {
        //取得或產生account_id
        $sAccount = $_oRequest->input('account'); //會員帳號
        $iHallID = 2;
        $aUserID = $_oUsers::getList($_aFields = ['id'], ['account'=>$sAccount, 'hallid'=>$iHallID]);
        if(empty($aUserID)) {//不存在則新增使用者
            $oNow = Carbon::today();
            $sPassword = $sAccount.'_'.Carbon::today()->toDateString();
            $iUserID = $_oUsers::add(
                $iHallID,
                $sAccount,
                Hash::make($sPassword),
                '', //電話
                '', //email
                null, //暱稱
                1, //停啟用
                1 //激活狀態
            );
        }else {
            $iUserID = $aUserID[0]['id'];
        }

        //產生商品Code
        $aProductCode = $_oProductList::getList(['code'], ['status'=>1]);
        $sProductCode = $aProductCode[array_rand($aProductCode)];

        //使用account 取得account_id
        $aRequest['account_id'] = $iUserID; //會員ID
        $aRequest['payway_id'] = $_oRequest->input('payway_id'); //交易付款ID 1:第三方超商付款, 2:第三方ATM付款, 3:銀行
        $aRequest['price'] = $_oRequest->input('price'); //募款金額
        $aRequest['bank_code'] = $_oRequest->input('bank_code', null); //銀行代碼
        $aRequest['bank_name'] = $_oRequest->input('bank_name', null); //銀行戶名
        $aRequest['bank_account'] = $_oRequest->input('bank_account', null); //銀行帳號
        $aRequest['product_code'] = $sProductCode;
        $aRequest['name'] = null;
        $aRequest['mobile'] = ''; //手機
        $aRequest['address'] = null; //地址
        $aRequest['deadline_days'] = 1; //付款截止天數
        $aRequest['pay_status'] = 0; //付款狀態 0未付款, 1已付款
        $aRequest['order_status'] = 0; //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $aRequest['delivery_status'] = 0; //出貨狀態 0待出貨, 1已出貨, -1出貨取消
        $aRequest['note'] = null;
        $aRequest['callback_code'] = $_oRequest->input('callback_code'); //廠商回傳代碼
        $aRequest['domain'] = $_oRequest->input('domain'); //domain

        $aPayment = $this->createOrderPayment(
            $aRequest,
            $_oProductList,
            $_oProductOrderList,
            $_oProductOrderDetail,
            $_oPaymentServices,
            $_oPaymentSetting
        );
        return ['result' => true, 'data' => $aPayment['data']];
    }

    /**
     * 新增訂單
     *
     * @param Request $_oRequest
     * @param ProductList $_oProductList
     * @param ProductOrderList $_oProductOrderList
     * @param ProductOrderDetail $_oProductOrderDetail
     * @return void
     */
    private function createOrderPayment(
        $_aRequest,
        $_oProductList,
        $_oProductOrderList,
        $_oProductOrderDetail,
        $_oPaymentServices,
        $_oPaymentSetting
    )
    {
        $iAccountId = $_aRequest['account_id']; //會員ID
        $sName = $_aRequest['name']; //寄信人名稱
        $iMobile = $_aRequest['mobile']; //手機
        $iPaywayId = $_aRequest['payway_id']; //交易付款ID 1:第三方超商付款, 2:第三方ATM付款, 3:銀行
        $sAddress = $_aRequest['address']; //地址
        $iDeadLineDay = $_aRequest['deadline_days']; //付款截止天數
        $iPayStatus = $_aRequest['pay_status']; //付款狀態 0未付款, 1已付款
        $iOrderStatus = $_aRequest['order_status']; //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $iDeliveryStatus = $_aRequest['delivery_status']; //出貨狀態 0待出貨, 1已出貨, -1出貨取消
        $iPriceTotal = $_aRequest['price']; //募款金額
        $sBankCode = $_aRequest['bank_code']; //銀行代碼
        $sBankName = $_aRequest['bank_name']; //銀行戶名
        $sBankAccount = $_aRequest['bank_account']; //銀行帳號
        $sProductCode = $_aRequest['product_code']; //商品代碼
        $sNote = $_aRequest['note']; //備註
        $sCallbackCode = $_aRequest['callback_code']; //廠商回傳代碼
        $sDomain = $_aRequest['domain']; //domain
        $aProductList = $_oProductList::getByCode($sProductCode, ['id', 'name', 'status']);

        //檢查訂貨商品
        if(empty($aProductList)) {
            throw new ProductOrderException('ORDER_PRODUCT_IS_NOT_EXIST');
        }

        //商品已被下架
        if($aProductList['status'] == 0) {
            throw new ProductOrderException('ORDER_PRODUCT_STATUS_ERROR');
        }

        //若付款為『銀行』，則檢查銀行資訊是否完整
        if($iPaywayId == 3 && in_array(null, [$sBankCode, $sBankName, $sBankAccount])) {
            throw new ProductOrderException('ORDER_PRODUCT_BANK_ERROR');
        }
        $aPaymentType = $_oPaymentSetting::getPaymentSetting()->toArray();
        $iPaymentType = -1;
        if(!empty($aPaymentType)) {
            $iPaymentType = $aPaymentType[0]['type'];
        }
        //訂單碼
        $sCode = $this->createOrderCode($iPaymentType);

        //新增訂單
        $iOrderId = $_oProductOrderList::add(
            $iAccountId,
            $aProductList['id'],
            $sName,
            $iMobile,
            $iPaywayId,
            $sAddress,
            $iPayStatus,
            $iOrderStatus,
            $iDeliveryStatus,
            $iPriceTotal,
            $iDeadLineDay,
            $sCode,
            $sNote,
            $sCallbackCode
        );

        $aRequest = [
            'order_id' => $iOrderId,
            'account_id' => $iAccountId,
            'code' => $sCode,
            'payway_id' => $iPaywayId,
            'amount' => $iPriceTotal,
            'callback_code' => $sCallbackCode,
            'domain' => $sDomain,
        ];

        //使用銀行付款，需塞銀行資訊
        if($iPaywayId == 3) {
            $aRequest['bank_code'] = $sBankCode;
            $aRequest['bank_name'] = $sBankName;
            $aRequest['bank_account'] = $sBankAccount;
        }

        $aPayment = $_oPaymentServices->createOrder($aRequest);
        if($aPayment['result'] == false) {
            $sMsg = $aPayment['message'];
            $aThrowKeys = [
                'user is not exists' => 'ORDER_PRODUCT_PAYMENT_USER_NOT_EXIST',
                'bankAccount is not exists' => 'ORDER_PRODUCT_BANK_ACCOUNT_NOT_EXIST',
                'ecpay error' => 'ORDER_PRODUCT_ECPAY_ERROR',
            ];

            $sThrowKey = isset($aThrowKeys[$sMsg]) ? isset($aThrowKeys[$sMsg]) : '';
            if($sThrowKey == '') { //非預期的錯誤
                throw new ProductOrderException('ORDER_PRODUCT_PAYMENT_ERROR');
            }
            throw new ProductOrderException($sThrowKey);
        }
        return $aPayment;
    }

    public function updateOrder(Request $_oRequest, ProductOrderList $_oProductOrderList, $_iOrderID)
    {
        $aRequest = $_oRequest->all();
        $aFieldsLimit = [ //更新限制
            'mobile', //手機號碼
            'payway_id', //交易付款ID
            'address', //地址
            'pay_status', //付款狀態 0未付款, 1已付款
            'order_status', //訂單狀態 0審核中, 1訂單通過, -1訂單取消
            'delivery_status', //出貨狀態 0待出貨, 1已出貨, -1出貨取消
            'reason', //訂單取消原因 或 出貨取消原因
        ];

        $aFields = [];
        foreach($aRequest as $sCol => $sVal) {
            if(in_array($sCol, $aFieldsLimit)) {
                $aFields[$sCol] = $sVal;
            }
        }

        if(auth()->user() == null) { //找不到admin user
            throw new ProductOrderException('ORDER_PRODUCT_UPDATE_NOT_FOUND_ADMIN');
        }

        $aFields['modify_admin_id'] = auth()->user()->toArray()['id'];
        $_oProductOrderList::updateById($_iOrderID, $aFields);

        return ['result' => true, 'data' => []];
    }

    public function getSelfOrderByCode(
        $_sProductCode,
        Request $_oRequest,
        ProductOrderList $_oProductOrderList,
        Users $_oUsers,
        AdminUser $_oAdminUser,
        ProductList $_oProductList
    )
    {
        $aRequest['order_status'] = null; //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $aRequest['delivery_status'] = null; //出貨狀態 0待出貨, 1已出貨, -1出貨取消
        $aRequest['start_date'] = null;
        $aRequest['end_date'] = null;
        $aRequest['limit'] = 0;
        $aRequest['account_id'] = auth()->user()->id;
        $aRequest['account'] = auth()->user()->account;
        $aRequest['order_id'] = null;
        $aRequest['mobile'] = null;
        $aRequest['payway_id'] = null;
        $aRequest['code'] = $_sProductCode; //訂單碼
        $aRequest['order_by'] = 'order_id'; //排序欄位
        $aRequest['order_type'] = 'desc'; //排序方式 升序或降序
        $aRequest['callback_code'] = null;
        $aFields = [
            'product_name',
            'price_total',
            'name',
            'address',
            'mobile',
            'note',
            'deadline',
            'payway_id',
            'order_datetime',
            'pay_status',
            'order_status',
        ];

        $aData = $this->getOrderList(
            $aRequest,
            $_oProductOrderList,
            $_oUsers,
            $_oAdminUser,
            $_oProductList,
            $aFields
        );

        $aOrderPayment = $this->getOrderPaymentByCode($_sProductCode);
        $result = [$aData['data'][0]->merge($aOrderPayment['data'])];

        return ['result' => true, 'data' => $result];
    }
    /**
     * 搜尋指定的會員存款訂單 - 銀行資訊 (by order_id)
     *
     * @param  integer $orderId
     *
     * @return array
     */
    public function getOrderPayment($orderId)
    {
        // 訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $aQuery = UsersDepositPayment::getPaymentByStatus($orderId);

        // 找不到此訂單
        if($aQuery->isEmpty()) return ['result' => true, 'data' => []];

        $result = [];
        $payway = $aQuery[0]['payway_info'];
        $deposit = $aQuery[0]['deposit_info'];

        switch ($aQuery[0]['payway_id']){
            case 1:
                $result['member'] = $payway['member'];
                $result['payment_no'] = $payway['payment_no'];
                $result['no'] = $payway['no'];
                $result['credit'] = $payway['credit'];
                $result['reason'] = $aQuery[0]['order']['reason'];
                break;
            case 2:
                $result['member'] = $payway['member'];
                $result['bank'] = $payway['code'].'-'.$payway['bank'];
                $result['bank_account'] = $payway['bank_account'];
                $result['no'] = $payway['no'];
                $result['credit'] = $payway['credit'];
                $result['reason'] = $aQuery[0]['order']['reason'];
                break;
            case 3:
                $result['member'] = $payway['member'];
                $result['bank'] = $payway['code'].'-'.$payway['bank'];
                $result['branch'] = $payway['branch'];
                $result['name'] = $payway['name'];
                $result['bank_account'] = $payway['account'];
                $result['mem_account'] = $deposit['code'].'-'.$deposit['bank'].'-'.substr($deposit['account'],-5);
                $result['credit'] = $payway['credit'];
                $result['reason'] = $aQuery[0]['order']['reason'];
                break;
        }
        $result['payway_id'] = $aQuery[0]['payway_id'];

        return ['result' => true, 'data' => $result];
    }

    /**
     * 搜尋指定的會員存款訂單 - 銀行資訊 (by Code)
     *
     * @param  integer $code
     *
     * @return array
     */
    private function getOrderPaymentByCode($code)
    {
        $order = ProductOrderList::getByCode($code);
        // 訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $aQuery = UsersDepositPayment::getPaymentByStatus($order[0]['order_id']);

        $result = [];
        $payway = $aQuery[0]['payway_info'];

        switch ($aQuery[0]['payway_id']){
            case 1:
                $result['member'] = $payway['member'];
                $result['payment_no'] = $payway['payment_no'];
                $result['credit'] = $payway['credit'];
                break;
            case 2:
                $result['member'] = $payway['member'];
                $result['bank'] = $payway['code'].' ('.$payway['bank'].')';
                $result['bank_account'] = $payway['bank_account'];
                $result['credit'] = $payway['credit'];
                break;
            case 3:
                $result['member'] = $payway['member'];
                $result['bank'] = $payway['code'].' ('.$payway['bank'].')';
                $result['branch'] = $payway['branch'];
                $result['name'] = $payway['name'];
                $result['bank_account'] = $payway['account'];
                $result['credit'] = $payway['credit'];
                break;
        }

        return ['result' => true, 'data' => $result];
    }

    /**
     * 產生訂單碼
     *
     * @param PaymentSetting $_oPaymentSetting
     * @return string (八碼的code)
     */
    private function createOrderCode($_iPaymentType)
    {
        $iCodeLen = 8; //訂單碼長度
        $sCode = '';
        if($_iPaymentType == 2) { //藍新只能英文數字跟底線
            $sChar = 'abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789_';
            $sCharLen = strlen($sChar) - 1;
            for ($i = 0; $i < $iCodeLen; $i++) {
                $sCode .= $sChar[rand(0, $sCharLen)];
            }
        } else {
            $sCode = Hash::make(time());
            $sCode = str_replace('/', '', $sCode); //去掉斜線，不然客端連結會有問題
            $sCode = substr($sCode, ($iCodeLen * -1));
        }
        return $sCode;
    }
}
