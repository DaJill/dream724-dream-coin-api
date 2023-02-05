<?php

use Illuminate\Database\Seeder;

class ProductImageTableSeeder extends Seeder
{
    private $table = 'product_image';
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
                'id'=>0, //商品ID
                'sort'=>0, //圖片排序
                'image_path'=>'', //商品圖片路徑
            ],
            [
                'id'=>0,
                'sort'=>1,
                'image_path'=>'',
            ],
            [
                'id'=>0,
                'sort'=>2,
                'image_path'=>'',
            ],
            [
                'id'=>0,
                'sort'=>3,
                'image_path'=>'',
            ],
            [
                'id'=>1,
                'sort'=>0,
                'image_path'=>'product_images/15737449390螢幕快照+2019-11-06+下午9.41.29.png',
            ],
            [
                'id'=>1,
                'sort'=>1,
                'image_path'=>'product_images/15737448990螢幕快照+2019-11-06+下午9.41.01.png',
            ],
            [
                'id'=>1,
                'sort'=>2,
                'image_path'=>'product_images/15732227001螢幕快照%202019-11-08%20下午10.17.08.png',
            ],
            [
                'id'=>1,
                'sort'=>3,
                'image_path'=>'',
            ],
            [
                'id'=>1,
                'sort'=>4,
                'image_path'=>'',
            ],
            [
                'id'=>1,
                'sort'=>5,
                'image_path'=>'',
            ],
            [
                'id'=>1,
                'sort'=>6,
                'image_path'=>'',
            ],
            [
                'id'=>1,
                'sort'=>7,
                'image_path'=>'',
            ],
            [
                'id'=>1,
                'sort'=>8,
                'image_path'=>'',
            ],
            [
                'id'=>2,
                'sort'=>0,
                'image_path'=>'',
            ],
            [
                'id'=>3,
                'sort'=>0,
                'image_path'=>'',
            ],
            [
                'id'=>4,
                'sort'=>0,
                'image_path'=>'',
            ],
            [
                'id'=>5,
                'sort'=>0,
                'image_path'=>'',
            ],
        ]);
    }
}
