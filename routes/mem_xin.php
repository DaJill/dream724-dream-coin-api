<?php
// 產生單
Route::domain(config('api.url.xin_api'))->group(function () {
    Route::post('product/order', 'Product\ProductOrder@addOrder')->middleware('validator.common:account@must','validator.deposit:payway_id@must','validator.product_order:callback_code@must','validator.product_order:domain@must','validator.product:price@must','validator.deposit:bank_code@null,bank_name@null,bank_account@null');
    Route::get('payment/list', 'Bank\Payway@getPaymentSet');
});
