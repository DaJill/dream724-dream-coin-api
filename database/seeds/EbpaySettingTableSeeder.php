<?php

use Illuminate\Database\Seeder;

class EbpaySettingTableSeeder extends Seeder
{
    private $table = 'ebpay_setting';
    private $db = 'User';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::connection($this->db)->table($this->table)->truncate();
        $settingList = [
            'local'      => [
                ['merchant_id' => 'MS39623574', 'hash_key' => 'kuw2JASDnzMPLG2sEhc03sgr6GknTkCa', 'hash_iv' => 'CnrbfmaOFRcfyhTP', 'api_url' => 'https://ccore.newebpay.com/MPG/mpg_gateway', 'notice_url' => 'https://dream-coin-api.test', 'active' => 1],
            ],
            'production' => [
                ['merchant_id' => '', 'hash_key' => '', 'hash_iv' => '', '' => '', 'api_url' => 'https://core.newebpay.com/MPG/mpg_gateway', 'notice_url' => 'https://api.dream-wr.net', 'active' => 1],
            ],
        ];
        $info = $settingList[env('APP_ENV')] ?? $settingList['local'];
        foreach ($info as $value) {
            DB::connection($this->db)->table($this->table)->insert(array_merge($value, [
                'created_at'      => \Carbon\Carbon::now(),
                'updated_at'      => \Carbon\Carbon::now(),
            ]));
        }
    }
}
