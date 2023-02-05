<?php

/*
|--------------------------------------------------------------------------
| 已登入
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth'])->group(function () {
    Route::post('user/login', 'User\User@login')->middleware('validator.common:account@must,password@must,hallid@must');
    Route::get('error/path/{code}', 'Error\Message@getErrorPathById')->where(['code' => '[0-9]+',]);
//
    /*
    |--------------------------------------------------------------------------
    | 後台管理
    |--------------------------------------------------------------------------
    */
    // 帳號管理
    Route::prefix('/admin_user')->namespace('Admin')->group(function () {
        // 列表
        Route::get('/', 'AdminUser@index');
        // 新增
        Route::post('/', 'AdminUser@store');
        // 修改
        Route::put('/{id}', 'AdminUser@update');
        // 刪除
        Route::delete('/{id}', 'AdminUser@destroy');
    });
    /*
    |--------------------------------------------------------------------------
    | 系統相關
    |--------------------------------------------------------------------------
    */
    Route::prefix('/system')->namespace('Admin')->group(function () {
        // 取得目前操作者資訊(用於前端檢查Token是否失效)
        Route::get('/auth_user', 'System@information');
        // 修改個人密碼
        Route::put('/change_self_password', 'System@updatePassword');
        // 登出
        Route::post('/logout', 'System@logout');
    });

    Route::post('product/create', 'Product\Product@addProduct')->middleware('validator.common:date@must','validator.product:name@must,description@null,price_target@must,status@null,funding_type@must,img@null');
    Route::put('product/stop/{id}', 'Product\Product@stopProduct')->where(['id' => '[0-9]+',]);
    Route::post('product/update/{id}', 'Product\Product@updateProduct')->middleware('validator.common:date@null','validator.product:name@null,description@null,price_target@null,status@null,funding_type@null,img@null')->where(['id' => '[0-9]+',]);
    Route::get('product/list/v2', 'Product\Product@getProductListByAdmin')->middleware('validator.common:order_by@null,order_type@null,limit@null,page@null,start_date@null,end_date@null,code@null,','validator.product:product_id@null,name@null,status@must');
    Route::get('product/{id}', 'Product\Product@getProductDetailByAdmin')->where(['id' => '[0-9]+',]);
    Route::get('bank/pay_list', 'Bank\Payway@getPaywayList');
    Route::get('bank/pay_limit', 'Bank\Payway@getPaywayOption');
    Route::put('bank/pay', 'Bank\Payway@updatePayway')->middleware('validator.common:setting_info@must');
    Route::put('product/order/{order_id}', 'Product\ProductOrder@updateOrder')->middleware('validator.common:mobile@null,address@null','validator.deposit:payway_id@null','validator.product_order:pay_status@null,order_status@null,delivery_status@null,reason@null')->where(['account_id' => '[0-9]+',]);
    Route::get('product/order/list/v2', 'Product\ProductOrder@getOrderListByAdmin')->middleware('validator.common:order_by@null,order_type@null,code@null,mobile@null,start_date@null,end_date@null,limit@null,page@null,account_id@null,account@null','validator.deposit:payway_id@null','validator.product_order:order_id@null,order_status@null,delivery_status@null,callback_code@null');
    Route::get('product/order/detail/{order_id}', 'Product\ProductOrder@getOrderDetail')->where(['order_id' => '[0-9]+',]);
    Route::get('product/order/payment/{order_id}', 'Product\ProductOrder@getOrderPayment')->where(['order_id' => '[0-9]+']);
    Route::get('bank/bank', 'Bank\Bank@getBank');
    Route::get('bank/deposit_account', 'Bank\DepositAccount@getDepositAccount');
    Route::post('bank/deposit_account', 'Bank\DepositAccount@createDepositAccount')->middleware('validator.deposit:bank_id@must,code@must,branch@must,name@must,account@must');
    Route::put('bank/deposit_account/{id}', 'Bank\DepositAccount@updateDepositAccount')->middleware('validator.deposit:bank_id@must,code@must,branch@must,name@must,account@must');
    Route::delete('bank/deposit_account/{id}', 'Bank\DepositAccount@deleteDepositAccount');
    Route::put('bank/deposit_account/active/{id}', 'Bank\DepositAccount@changeAccountActive');
    Route::put('bank/deposit_account/clear/{id}', 'Bank\DepositAccount@clearAccountCredit');
    Route::get('bank/deposit/dropdown/status', 'Bank\Deposit@getOption')->middleware('validator.common:condition@must');
    Route::get('bank/deposit/dropdown/payway', 'Bank\Deposit@getPayOption')->middleware('validator.common:condition@must');
    Route::get('bank/deposit/{status}/total', 'Bank\Deposit@getDepositCount')->middleware('validator.common:start_time@null,end_time@null','validator.deposit:payway_id@must,no@null,account@null');
    Route::get('bank/deposit/{status}/list', 'Bank\Deposit@getDepositList')->middleware('validator.common:start_time@null,end_time@null','validator.deposit:payway_id@must,no@null,account@null');
    Route::put('bank/deposit/{id}', 'Bank\Deposit@updateDeposit')->middleware('validator.deposit:action@must,reason@null');
    Route::get('user/{account_id}', 'User\User@getUserById')->where(['account_id' => '[0-9]+',]);
    Route::get('user/list', 'User\User@getUserList')->middleware('validator.common:account_id@null,hallid@null,token_active@null,active@null,account@null,nickname@null,mobile@null,email@null,start_date@null,end_date@null,order_by@null,order_type@null,limit@null,page@null');
    Route::get('user/usual_address/{account_id}', 'User\User@getUsualAddress')->where(['account_id' => '[0-9]+',]);
    Route::post('user/usual_address/{account_id}', 'User\User@addUsualAddress')->middleware('validator.common:address@must,convenience_store@null,mobile@must,name@must')->where(['account_id' => '[0-9]+',]);
    Route::put('user/usual_address/{id}', 'User\User@modifyUsualAddress')->middleware('validator.common:address@null,convenience_store@null,mobile@null,name@null')->where(['id' => '[0-9]+',]);
    Route::delete('user/usual_address/{id}', 'User\User@delectUsualAddress')->where(['id' => '[0-9]+',]);
    Route::get('user/order/{account_id}', 'User\User@getOrder')->where(['account_id' => '[0-9]+',]);
    Route::get('stats/dashboard/{hall_id}', 'Stats\Dashboard@getHome')->where(['account_id' => '[0-9]+',]);
    Route::put('user/{account_id}', 'User\User@updateUserByAdmin')->middleware('validator.common:hallid@null,account@null,nickname@null,password@null,mobile@null,email@null,memo@null,token_active@null,active@null');
});

/*
|--------------------------------------------------------------------------
| 未登入
|--------------------------------------------------------------------------
*/

// 後台登入驗證
Route::post('/system/login', 'Admin\System@login');
