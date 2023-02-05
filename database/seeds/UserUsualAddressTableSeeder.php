<?php

use Illuminate\Database\Seeder;

class UserUsualAddressTableSeeder extends Seeder
{
    private $table = 'user_usual_address';
    private $db = 'User';
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
                'account_id' => 1,
                'address' => '台中市',
                'convenience_store' => '',
                'name'=>'菜伊文',
                'mobile'=>'0940520940',
            ],
            [
                'account_id' => 1,
                'address' => '台北',
                'convenience_store' => '',
                'name'=>'送此魚',
                'mobile'=>'0988888888',
            ],
            [
                'account_id' => 1,
                'address' => '花蓮',
                'convenience_store' => '7-11超商取貨',
                'name'=>'含狗與',
                'mobile'=>'09487087487',
            ],
            [
                'account_id' => 2,
                'address' => '台中市',
                'convenience_store' => '全家超商取貨 A門市',
                'name'=>'勾台敏',
                'mobile'=>'0996996996',
            ],
        ]);
    }
}
