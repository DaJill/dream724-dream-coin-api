<?php

use Illuminate\Database\Seeder;

class ProductListTableSeeder extends Seeder
{
    private $table = 'product_list';
    private $db = 'Product';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection($this->db)->table($this->table)->truncate();
        DB::connection($this->db)->table($this->table)->insert([
            [
                'code'=>substr(Hash::make(time()), -8), //專案碼
                'name'=>'米家掃地機器人', //專案名稱
                'description'=>'
                同步定位與地圖建構算法，智慧規劃路徑
                以5×360°/秒 的速度掃描房間，取得距離訊息
                最大風壓澎湃吸力，動態調速邊刷，1公分沿牆清掃
                採用先沿邊後Z字形清掃路徑，逐一完成分區清掃
                大電池持久清掃，自動規劃最短路線返回充電座
                電量低於20%能自動回充，充至80%後，能斷點續掃
                最大續航時間約 2.5 小時，最大清掃面積約 75 坪
                遠端智慧遙控，即時查看清掃地圖', //專案說明
                'price_target'=>789500, //募款目標
                'end_date' => '2030-01-01',
                'status'=>1, //狀態 0下架, 1上架
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'name'=>'SONY PS4 Pro主機CUH-7218系列 1TB-極致黑',
                'description'=>'
                內含精選遊戲任選兩片
                ■滿足追求更高層次遊戲體驗的玩家
                ■超高解析度，如臨其境震撼視覺享受
                ■GPU性能升級，更細緻流暢、穩定的畫面
                ■支援HDR，更逼真生動，如真實世界般視覺效果
                ■外型三段傾斜設計具強烈存在感，中央點綴鏡面LOGO
                原廠一年保固，產品保固採線上登記',
                'price_target'=>1298000,
                'end_date' => '2030-12-20',
                'status'=>1,
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'name'=>'Petree喵星球自動貓砂盆 (台灣總代公司貨）',
                'description'=>'
                ◆兩側按鈕設計，弧形頂蓋，可一鍵拆卸，快捷方便
                ◆抗菌材料球體，PP材質注入抗菌材質，耐髒不易霉化
                ◆可拆腳踏，工程學結構設計，開口方便且易乾
                ◆穩固弧形轉角底座，設備感應智能控制中心，不變形，不可水洗
                ◆超大空間，球體結構，貓咪安心拉拉
                ◆適合使用膨潤土砂',
                'price_target'=>1480000,
                'end_date' => '2025-10-31',
                'status'=>1,
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'name'=>'DR.MANGO 100吋16:9便攜型投影機高畫質布幕',
                'description'=>'
                ★看電影更享受、辦公更便捷、唱K更盡興、遊戲更歡樂~
                ★方便攜帶，安裝簡易
                ',
                'price_target'=>69900,
                'end_date' => '2022-02-28',
                'status'=>0,
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'name'=>'SONY 4K HDR液晶電視 KD-65X9500G',
                'description'=>'
                ●超極真影像處理器 X1 旗艦版
                ●X-Motion Clarity 極瞬明銳影像技術
                ●超能直下式 LED 背光技術
                ●多音域環繞聲場技術
                ●低音反射型揚聲器
                ●Netflix 校正模式',
                'price_target'=>9990000,
                'end_date' => '2021-11-11',
                'status'=>1,
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'name'=>'HITACHI日立22L過熱水蒸氣烘烤微波爐 MROVS700T',
                'description'=>'
                ■ 紅外線溫度感測
                ■ 高溫達攝氏250度
                ■ 過熱水蒸氣健康調理
                ■ 92道自動料理/125道食譜
                ■ 可貼壁設計
                ■ 蒸氣除垢一鍵脫臭',
                'price_target'=>1690000,
                'end_date' => '2020-08-08',
                'status'=>1,
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'name'=>'Panasonic 11公斤nanoeX滾筒洗衣機 NA-VX88GL (左開)',
                'description'=>'
                ECONAVI智慧節能
                溫水槽洗淨+nanoeX抑制黑黴
                洗劑自動投入
                溫風除濕式烘乾
                衛生除蹣行程搭載
                四段溫水泡洗淨(15℃/30℃/40℃/60℃)
                雙效自動槽洗淨',
                'price_target'=>5580000,
                'end_date' => '2021-01-15',
                'status'=>1,
            ],
        ]);
    }
}
