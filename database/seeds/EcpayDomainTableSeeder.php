<?php

use Illuminate\Database\Seeder;

class EcpayDomainTableSeeder extends Seeder
{
    private $table = 'ecpay_domain';
    private $db = 'User';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection($this->db)->table($this->table)->truncate();
        $DomainList = [
            'local'      => [
                ['url' => 'https://dream-coin-api.test', 'active' => 1, 'sort' => 0],
            ],
            'production' => [
                ['url' => 'http://localhost', 'active' => 1, 'sort' => 0],
            ],
        ];
        $info = $DomainList[env('APP_ENV')] ?? $DomainList['local'];
        foreach ($info as $value) {
            DB::connection($this->db)->table($this->table)->insert(array_merge($value, [
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]));
        }
    }

}
