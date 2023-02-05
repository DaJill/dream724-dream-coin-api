<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserTableSeeder extends Seeder
{
    private $table = 'admin_user';
    private $connection = 'User';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 系統初始後台帳號資訊
        $adminUser = [
            ['id' => 1, 'account' => 'dreamadmin', 'password' => Hash::make('password'), 'name' => '管理人員'],
            ['id' => 2, 'account' => 'jilladmin', 'password' => Hash::make('password'), 'name' => '管理人員之可愛大街街'],
            ['id' => 3, 'account' => 'yuriadmin', 'password' => Hash::make('password'), 'name' => '管理人員之討厭尤利利'],
            ['id' => 4, 'account' => 'mickyadmin', 'password' => Hash::make('password'), 'name' => '管理人員之前端馬琪琪'],
            ['id' => 5, 'account' => 'dopingadmin', 'password' => Hash::make('password'), 'name' => '管理人員之體育禁藥藥'],
        ];

        foreach ($adminUser as $params) {
            $user = $this->getDb()->find($params['id']);
            if ($user == null) {
                $this->getDb()->insert(array_merge($params, [
                    'token'      => '',
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]));
            }
        }
    }

    /**
     * 取得後台帳號資料連線元件
     *
     * @return object
     */
    private function getDb()
    {
        return DB::connection($this->connection)->table($this->table);
    }
}
