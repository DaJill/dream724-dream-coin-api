<?php

return [
    'common' => [
        'code' => '001',
        'parameter' => [
            'account' => 'alpha_dash|between:3,20', //帳號
            'password' => 'string|between:6,20|regex:/(?=.*[0-9])(?=.*[a-z]).{6,20}/', //密碼
            'hallid' => 'integer', //平台id
            'order_type' => 'string|in:asc,desc',//排序 asc升序, desc降序
            'order_by' => 'string', //排序的欄位
            'limit' => 'integer|min:1', //一頁筆數(從1開始)
            'fields' => 'array', //回傳欄位
            'fields_condition' => 'array', //欄位條件
            'setting_info' => 'array', //設定資訊
            'account_id' => 'integer', //會員ID
            'mobile' => 'string', //手機
            'payment' => 'integer|min:0|max:2', //支付方式 0點數, 1銀行, 2第三方
            'address' => 'string', //地址
            'start_date' => 'date_format:Y-m-d|before_or_equal:end_date|required_with:end_date', //開始時間
            'end_date' => 'date_format:Y-m-d|after_or_equal:start_date|required_with:start_date', //結束時間
            'page' => 'integer|min:1', //頁數
            'code' => 'string', //加密編碼
            'payments' => 'array', //支付方式 [0點數, 1銀行, 2第三方]
            'condition' => 'string', //條件
            'active' => 'integer|min:-1|max:1', //狀態 -1停用, 1啟用
            'token_active' => 'integer|min:0|max:1', //激活 0未激活, 1已激活
            'nickname' => 'string', //暱稱
            'convenience_store' => 'string', //超商
            'email' => 'email', //信箱
            'name' => 'string', //名稱
            'start_time' => 'date_format:Y-m-d H:i:s|before_or_equal:start_time|required_with:end_time', //開始時間
            'end_time' => 'date_format:Y-m-d H:i:s|after_or_equal:start_time|required_with:end_time', //結束時間
            'date' => 'date_format:Y-m-d', //日期
            'memo' => 'string', //備註
        ],
        'exception' => [
            'account' => ['001', 'account is invalid', '帳號格式錯誤'],
            'password' => ['002', 'password is invalid', '密碼格式錯誤'],
            'hallid' => ['003', 'hallid is invalid', '平台id格式錯誤'],
            'order_type' => ['004', 'order type is invalid', '排序格式錯誤'],
            'order_by' => ['005', 'order by is invalid', '排序欄位格式錯誤'],
            'limit' => ['006', 'limit is invalid', '一頁筆數格式錯誤'],
            'fields' => ['007', 'fields is invalid', '回傳欄位格式錯誤'],
            'fields_condition' => ['008', 'fields condition is invalid', '欄位條件格式錯誤'],
            'setting_info' => ['009', 'setting_info is invalid', '設定格式錯誤'],
            'account_id' => ['010', 'account id is invalid', '地址格式錯誤'],
            'mobile' => ['011', 'mobile is invalid', '手機格式錯誤'],
            'payment' => ['012', 'payment is invalid', '支付方式格式錯誤'],
            'address' => ['013', 'address is invalid', '地址格式錯誤'],
            'start_date' => ['014', 'start date is invalid', '開始時間格式錯誤'],
            'end_date' => ['015', 'end date is invalid', '結束時間格式錯誤'],
            'page' => ['016', 'page is invalid', '頁數格式錯誤'],
            'code' => ['017', 'code is invalid', '加密編碼格式錯誤'],
            'payments' => ['018', 'payments is invalid', '支付方式(多個)格式錯誤'],
            'condition' => ['019', 'condition is invalid', '條件格式錯誤'],
            'active' => ['020', 'active is invalid', '停啟用格式錯誤'],
            'token_active' => ['021', 'token active is invalid', '激活認證格式錯誤'],
            'nickname' => ['022', 'nickname is invalid', '暱稱格式錯誤'],
            'convenience_store' => ['023', 'convenience store is invalid', '超商格式錯誤'],
            'email' => ['024', 'email is invalid', '信箱格式錯誤'],
            'name' => ['025', 'name is invalid', '名稱格式錯誤'],
            'start_time' => ['026', 'start datetime is invalid', '開始時間格式錯誤'],
            'end_time' => ['027', 'end datetime is invalid', '結束時間格式錯誤'],
            'date' => ['028', 'date is invalid', '日期格式錯誤'],
            'memo' => ['029', 'memo is invalid', '備註格式錯誤'],
        ],
    ],
    'product' => [
        'code' => '002',
        'parameter' => [
            'name' => 'string', //商品名稱
            'description' => 'string', //描述
            'price' => 'integer', //價格
            'stock' => 'integer', //存貨
            'selling_volume' => 'integer', //銷售量
            'status' => 'integer|min:0|max:1', //上架狀態 0下架 1上架
            'delivery_price' => 'array', //寄送方式與寄送金額
            'img' => 'array',
            'shipping_fee' => 'integer|min:0|max:1',//運費規定 0免運, 1買家自付
            'product_id' => 'integer',//商品ID
            'funding_type' => 'array',//回饋設定
            'price_target' => 'integer',//募款資金目標
        ],
        'exception' => [
            'name' => ['001', 'name is invalid', '名稱格式錯誤'],
            'description' => ['002', 'description is invalid', '說明格式錯誤'],
            'price' => ['003', 'price is invalid', '價格格式錯誤'],
            'stock' => ['004', 'stock is invalid', '存貨格式錯誤'],
            'selling_volume' => ['005', 'selling volumername is invalid', '銷售量格式錯誤'],
            'status' => ['006', 'status is invalid', '上架狀態格式錯誤'],
            'delivery_price' => ['007', 'delivery and price is invalid', '寄送方式與寄送金額格式錯誤'],
            'img' => ['008', 'img is invalid', '圖片格式錯誤'],
            'shipping_fee' => ['009', 'shipping_fee is invalid', '運費規定格式錯誤'],
            'product_id' => ['010', 'product id is invalid', '商品ID格式錯誤'],
            'funding_type' => ['011', 'funding type is invalid', '回饋設定格式錯誤'],
            'price_target' => ['012', 'price target is invalid', '募款資金目標格式錯誤'],
        ],
    ],
    'product_order' => [
        'code' => '003',
        'parameter' => [
            'pay_status' => 'integer|min:0|max:1', //付款狀態 0未付款, 1已付款
            'order_status' => 'integer|min:-1|max:1', //訂單狀態 0審核中, 1訂單通過, -1訂單取消
            'delivery_status' => 'integer|min:-1|max:1', //出貨狀態 0待出貨, 1已出貨, -1出貨取消
            'purchase' => 'array', //購買商品與數量 [{product_id}=>{count}, ...]
            'deadline_days' => 'integer', //付款截止日
            'order_id' => 'integer', //訂單ID
            'reason' => 'string', //訂單取消原因 或 出貨取消原因
            'note' => 'string', //會員寫入的備註
            'callback_code' => 'string', //回call碼
            'domain' => 'string',
            'callback_code' => 'string',//'廠商回傳代碼
        ],
        'exception' => [
            'pay_status' => ['001', 'pay status is invalid', '付款狀態格式錯誤'],
            'order_status' => ['002', 'order status is invalid', '訂單狀態格式錯誤'],
            'delivery_status' => ['003', 'delivery status is invalid', '出貨狀態格式錯誤'],
            'purchase' => ['004', 'purchase is invalid', '購買商品與數量格式錯誤'],
            'deadline_days' => ['005', 'deadline days is invalid', '付款截止日格式錯誤'],
            'order_id' => ['006', 'order id is invalid', '訂單ID格式錯誤'],
            'reason' => ['007', 'reason is invalid', '取消原因格式錯誤'],
            'note' => ['008', 'note is invalid', '會員寫入的備註格式錯誤'],
            'callback_code' => ['009', 'callback code is invalid', '回call碼格式錯誤'],
            'domain' => ['010', 'domain is invalid', '網址錯誤'],
            'callback_code' => ['013', 'call back code is invalid', '廠商回傳代碼格式錯誤'],
        ],
    ],
    'deposit' => [
        'code'      => '004',
        'parameter' => [
            'bank_id'   => 'integer',//銀行資料 table_id
            'code'      => 'string|between:3,3', //銀行代碼
            'branch'    => 'string|between:1,10', //分行名稱
            'account'   => 'string|between:1,20', //會員帳號&銀行帳號
            'name'      => 'string|between:1,20', //戶名
            'no'        => 'alpha_num|max:50', //訂單編號
            'payway_id' => 'integer|in:0,1,2,3', //付款類型
            'action'    => 'integer|in:1,2', //訂單狀態
            'reason'    => 'string', //取消原因
            'bank_code' => 'string|between:3,3', //銀行代碼
            'bank_name' => 'string|between:1,20', //戶名
            'bank_account'=> 'string|between:1,20', //銀行帳號
        ],
        'exception' => [
            'bank_id'   => ['001', 'bank_id is invalid', '銀行資料id格式錯誤'],
            'code'      => ['002', 'code is invalid', '銀行代碼格式錯誤'],
            'branch'    => ['003', 'branch is invalid', '分行名稱格式錯誤'],
            'account'   => ['004', 'account is invalid', '銀行帳號格式錯誤'],
            'name'      => ['005', 'name is invalid', '戶名格式錯誤'],
            'no'        => ['006', 'no is invalid', '訂單編號格式錯誤'],
            'payway_id' => ['007', 'payway_id is invalid', '付款類型格式錯誤'],
            'action'    => ['008', 'action is invalid', '訂單狀態格式錯誤'],
            'reason'    => ['009', 'reason is invalid', '取消原因格式錯誤'],
            'bank_code'    => ['010', 'bank code is invalid', '銀行代碼格式錯誤'],
            'bank_name'    => ['011', 'bank name is invalid', '戶名格式錯誤'],
            'bank_account'    => ['012', 'bank account is invalid', '銀行帳號格式錯誤'],
        ],
    ],

];
