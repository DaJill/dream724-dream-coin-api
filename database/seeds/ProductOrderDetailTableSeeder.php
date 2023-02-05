<?php
use Illuminate\Database\Seeder;

class ProductOrderDetailTableSeeder extends Seeder
{
    private $table = 'product_order_detail';
    private $db = 'Product';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection($this->db)->table($this->table)->truncate();
        $aInsert = [
            [
                'order_id' => 0, //訂單ID
                'product_id' => 0, //商品ID
                'name' => 'aaa', //商品名稱
                'price' => rand(1,10000), //商品單價
                'count' => rand(1,10), //商品數量
            ],
            [
                'order_id' => 0,
                'product_id' => 1,
                'name' => 'bbb',
                'price' => rand(1,10000),
                'count' => rand(1,10),
            ],
            [
                'order_id' => 0,
                'product_id' => 2,
                'name' => 'ccc',
                'price' => rand(1,10000),
                'count' => rand(1,10),
            ],
            [
                'order_id' => 1,
                'product_id' => 1,
                'name' => 'bbb',
                'price' => rand(1,10000),
                'count' => rand(1,10),
            ],
            [
                'order_id' => 1,
                'product_id' => 4,
                'name' => 'fff',
                'price' => rand(1,10000),
                'count' => rand(1,10),
            ],
            [
                'order_id' => 2,
                'product_id' => 4,
                'name' => 'fff',
                'price' => rand(1,10000),
                'count' => rand(1,10),
            ],
            [
                'order_id' => 3,
                'product_id' => 4,
                'name' => 'fff',
                'price' => rand(1,10000),
                'count' => rand(1,10),
            ],
            [
                'order_id' => 4,
                'product_id' => 2,
                'name' => 'www',
                'price' => rand(1,10000),
                'count' => rand(1,10),
            ],
        ];
        
        DB::connection($this->db)->table($this->table)->insert($aInsert);
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
