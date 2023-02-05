<?php
// 綠界
Route::prefix('ecpay')->namespace('Payment')->group(function () {
    // 綠界回傳付款相關資訊
    Route::post('receive', 'Ecpay@receive');
    // 綠界付款完成通知回傳的網址
    Route::post('notice', 'Ecpay@notice');
    // 導轉
    Route::get('index', 'Ecpay@redirection');
    // 綠界產生訂單 - 測試用
    //Route::post('create', 'Ecpay@create');
    //Route::post('order', 'Ecpay@order');
});
// 藍新
Route::prefix('ebpay')->namespace('Payment')->group(function () {
    // 回傳付款相關資訊
    Route::post('receive', 'Ebpay@receive');
    // 付款完成通知回傳的網址
    Route::post('notice', 'Ebpay@notice');
});

