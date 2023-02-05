<?php

use Illuminate\Database\Seeder;

class PaymentSettingTableSeeder extends Seeder
{

    private $table = 'payment_setting';
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
                'type'        => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ]);
    }
}
