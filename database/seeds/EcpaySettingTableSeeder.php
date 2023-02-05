<?php

use Illuminate\Database\Seeder;

class EcpaySettingTableSeeder extends Seeder
{
    private $table = 'ecpay_setting';
    private $db = 'User';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ecpayDomainID = 1;
        $ecpayDomain = DB::connection($this->db)->table('ecpay_domain')->find($ecpayDomainID);
        if ($ecpayDomain == null) {
            dd('請先新增 ecpay_domain 資訊');
        }

        DB::connection($this->db)->table($this->table)->truncate();
        $settingList = [
            'local'      => [
                ['merchant_id' => '2000132', 'hash_key' => '5294y06JbISpM5x9', 'hash_iv' => 'v77hoKGq4kWxNNIS', 'api_url' => 'https://payment-stage.ecpay.com.tw', 'ecpay_domain_id' => 1, 'active' => 1],
            ],
            'production' => [
                ['merchant_id' => '', 'hash_key' => '', 'hash_iv' => '', '' => '', 'api_url' => 'https://payment.ecpay.com.tw', 'ecpay_domain_id' => 1, 'active' => 1],
            ],
        ];
        $info = $settingList[env('APP_ENV')] ?? $settingList['local'];
        foreach ($info as $value) {
            DB::connection($this->db)->table($this->table)->insert(array_merge($value, [
                'ecpay_domain_id' => $ecpayDomainID,
                'created_at'      => \Carbon\Carbon::now(),
                'updated_at'      => \Carbon\Carbon::now(),
            ]));
        }
    }
}
