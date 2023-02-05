<?php

use Illuminate\Database\Seeder;

class PaywaysTableSeeder extends Seeder
{

    private $table = 'payways';
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
                'id'          => 1,
                'type'        => 1,
                'name'        => '超商付款',
                'upper_limit' => 5000,
                'lower_limit' => 500,
                'amount'      => json_encode([500, 1000, 3000, 5000, 6000]),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id'          => 2,
                'type'        => 2,
                'name'        => 'ATM',
                'upper_limit' => 30000,
                'lower_limit' => 500,
                'amount'      => json_encode([500, 1000, 3000, 5000, 10000, 20000, 30000]),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id'          => 3,
                'type'        => 3,
                'name'        => '銀行卡',
                'upper_limit' => 500000,
                'lower_limit' => 500,
                'amount'      => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ]);
    }
}
