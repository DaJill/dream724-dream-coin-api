<?php

use Illuminate\Database\Seeder;

class BanksTableSeeder extends Seeder
{

    private $table = 'banks';
    private $db = 'User';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection($this->db)->table($this->table)->truncate();
        $json = File::get("database/data/bank.json");
        $data = json_decode($json);

        foreach($data as $obj){
            DB::connection($this->db)->table($this->table)->insert([
                'code' => $obj->code,
                'name' => $obj->name,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }

    }
}
