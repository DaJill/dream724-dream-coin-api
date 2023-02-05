<?php
Route::post('user/login', 'User\User@login')->middleware('validator.common:account@must,password@must');
Route::post('user/create', 'User\User@createMemAccount')->middleware('validator.common:account@must,password@must,mobile@null,email@must,nickname@null');
Route::put('user/account/active/{token}', 'User\User@activeAccount')->where(['token' => '[^/]+']);
Route::get('product/list', 'Product\Product@getProductListByMem')->middleware('validator.common:order_by@null,order_type@null,limit@null,page@null,start_date@null,end_date@null');
Route::post('user/reset/password', 'User\User@createResetAccount')->middleware('validator.common:email@must');
Route::put('user/reset/password/{token}', 'User\User@resetAccount')->middleware('validator.common:password@must')->where(['token' => '[^/]+']);
Route::get('product/{product_code}', 'Product\Product@getProductDetailByMem')->where(['product_code' => '[^/]+']);
Route::post('user/create/resend', 'User\User@resendCreateMail')->middleware('validator.common:email@must');


//登入後才可call
Route::middleware(['jwt.auth'])->group(function () {
    Route::get('user', 'User\User@getSelf');
    Route::post('product/order/{product_code}', 'Product\ProductOrder@addOrderMem')->middleware('validator.common:address@must,mobile@null','validator.deposit:payway_id@must','validator.product:price@must,name@must','validator.deposit:bank_code@null,bank_name@null,bank_account@null','validator.product_order:note@null')->where(['product_code' => '[^/]+']);
    Route::put('user', 'User\User@updateSelf')->middleware('validator.common:password@null,nickname@null,mobile@null,email@null');
    Route::get('product/order/list', 'Product\ProductOrder@getOrderListByMem')->middleware('validator.common:order_by@null,order_type@null,start_date@null,end_date@null,limit@null,page@null','validator.product_order:order_status@null,delivery_status@null');
    Route::post('user/logout', 'User\User@logout');
    Route::get('product/order/{product_code}', 'Product\ProductOrder@getSelfOrderByCode')->where(['product_code' => '[^/]+']);
    Route::get('bank/bank1177', 'Bank\Bank@getBank1177');
    Route::get('payment/list', 'Bank\Payway@getPaymentSet');
});
