<?php
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductOrderListTableSeeder extends Seeder
{
    private $table = 'product_order_list';
    private $db = 'Product';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection($this->db)->statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::connection($this->db)->table($this->table)->truncate();
        $aInsert = [
            [
                'code'=>substr(Hash::make(time()), -8), //訂單碼
                'account_id' => 1, //訂購人ID
                'product_id' => 1,
                'name' => 'peter', //寄件人姓名
                'mobile' => '09'.$this->randomString(8, '1234567890'), //手機號碼
                'payway_id' => 1, //交易付款ID
                'address' => '台中市南屯區xxxx', //地址
                'pay_status' => 0, //付款狀態 0未付款, 1已付款
                'order_status' => 0, //訂單狀態 0審核中, 1訂單通過, -1訂單取消
                'delivery_status' => 0, //出貨狀態 0待出貨, 1已出貨, -1出貨取消
                'modify_admin_id' => null, //最後異動管理者
                'reason' => '', //取消原因
                'note' => null,//會員備註
                'price_total' => 1000, //總金額
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'account_id' => 1,
                'product_id' => 2,
                'name' => '小叮噹',
                'mobile' => '09'.$this->randomString(8, '1234567890'),
                'payway_id' => 1,
                'address' => '台南市xxxxxx',
                'pay_status' => 0,
                'order_status' => 1,
                'delivery_status' => 0,
                'modify_admin_id' => 2,
                'reason' => '',
                'note' => null,
                'price_total' => 2000,
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'account_id' => 3,
                'product_id' => 3,
                'name' => '哆啦a夢',
                'mobile' => '09'.$this->randomString(8, '1234567890'),
                'payway_id' => 1,
                'address' => '台北市xxxxxx',
                'pay_status' => 0,
                'order_status' => -1,
                'delivery_status' => 0,
                'modify_admin_id' => 3,
                'reason' => '商品缺貨',
                'note' => null,
                'price_total' => 3000,
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'account_id' => 2,
                'product_id' => 1,
                'name' => 'ドラえもん',
                'mobile' => '09'.$this->randomString(8, '1234567890'),
                'payway_id' => 2,
                'address' => '花蓮市xxxxxx',
                'pay_status' => 1,
                'order_status' => 1,
                'delivery_status' => -1,
                'modify_admin_id' => 4,
                'reason' => '會員取消',
                'note' => null,
                'price_total' => 4000,
            ],
            [
                'code'=>substr(Hash::make(time()), -8),
                'account_id' => 2,
                'product_id' => 2,
                'name' => 'Jill',
                'mobile' => '09'.$this->randomString(8, '1234567890'),
                'payway_id' => 3,
                'address' => '台中市xxxxxx',
                'pay_status' => 1,
                'order_status' => 1,
                'delivery_status' => 1,
                'modify_admin_id' => 5,
                'reason' => '',
                'note' => null,
                'price_total' => 5000,
            ],

        ];
        foreach($aInsert as $iKey => $aRow) {
            $dtTime = Carbon::create(2019, rand(1,12), rand(1,28), rand(0,23), rand(0,59), rand(0,59));
            //訂購時間
            $aRow['order_datetime'] = $dtTime->toDateTimeString();

            //付款截止時間
            $aRow['deadline'] = $dtTime->modify('+7 days')->toDateTimeString();
            $aInsert[$iKey] = $aRow;
        }

        DB::connection($this->db)->table($this->table)->insert($aInsert);
        DB::connection($this->db)->statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function randomString($_iLength = 8, $_sCharacters) {
        $sCharacters = $_sCharacters;
        $sCharactersLength = strlen($sCharacters);
        $sRandomString = '';
        for ($i = 0; $i < $_iLength; $i++) {
            $sRandomString .= $sCharacters[rand(0, $sCharactersLength - 1)];
        }
        return $sRandomString;
    }
}
