<?php

use Illuminate\Database\Seeder;

class FundingType extends Seeder
{
    private $table = 'funding_type';
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
                'id'=>0, //專案ID
                'sort'=>0, //排序
                'description'=>'放大燈', //內容摘要
                'price'=>100, //回饋金額
                'delivery_date'=>'2020-09-01', //預計出貨日
            ],
            [
                'id'=>0,
                'sort'=>1,
                'description'=>'縮小燈',
                'price'=>200,
                'delivery_date'=>'2020-09-02',
            ],
            [
                'id'=>0,
                'sort'=>2,
                'description'=>'放大燈+縮小燈 大小通吃假！讓你巨大又迷你！',
                'price'=>250,
                'delivery_date'=>'2025-09-02',
            ],
            [
                'id'=>1,
                'sort'=>0,
                'description'=>'杯子',
                'price'=>100,
                'delivery_date'=>'2020-11-02',
            ],
            [
                'id'=>1,
                'sort'=>1,
                'description'=>'有機翼杯子，單身價！',
                'price'=>5000,
                'delivery_date'=>'2020-11-11',
            ],
            [
                'id'=>2,
                'sort'=>0,
                'description'=>'爸爸有錢價，一個零錢包',
                'price'=>8857,
                'delivery_date'=>'2022-08-08',
            ],
            [
                'id'=>3,
                'sort'=>0,
                'description'=>'爸爸就是有錢價，一個皮夾',
                'price'=>30000,
                'delivery_date'=>'2023-08-08',
            ],
            [
                'id'=>4,
                'sort'=>0,
                'description'=>'爸爸就是有錢價，一個皮夾',
                'price'=>30000,
                'delivery_date'=>'2023-08-08',
            ],
            [
                'id'=>5,
                'sort'=>0,
                'description'=>'爸爸就是有錢價，一個皮夾',
                'price'=>30000,
                'delivery_date'=>'2023-08-08',
            ],
            [
                'id'=>6,
                'sort'=>0,
                'description'=>'爸爸就是有錢價，一個皮夾',
                'price'=>30000,
                'delivery_date'=>'2023-08-08',
            ],
            [
                'id'=>7,
                'sort'=>0,
                'description'=>'爸爸就是有錢價，一個皮夾',
                'price'=>30000,
                'delivery_date'=>'2023-08-08',
            ],
        ]);
    }
}
