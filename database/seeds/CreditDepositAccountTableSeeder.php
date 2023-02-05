<?php

use Illuminate\Database\Seeder;

class CreditDepositAccountTableSeeder extends Seeder
{
    private $table = 'credit_deposit_account';
    private $db = 'User';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $params = [
            'bank_id'       => 1,
            'code'       => '004',
            'branch'     => '北屯',
            'account'    => '1688888888',
            'name'       => '發大財專戶',
            'active'     => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ];
        $check = DB::connection($this->db)->table($this->table)->select('id')->get()->count();
        $exist = DB::connection($this->db)->table($this->table)->select('id')->where('account', $params['account'])->first();
        if ($check == 0 && $exist == null) {
            DB::connection($this->db)->table($this->table)->insert($params);
        }

    }

}
