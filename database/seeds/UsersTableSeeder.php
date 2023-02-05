<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class UsersTableSeeder extends Seeder
{
    private $table = 'users';
    private $db = 'User';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $_oFaker)
    {
        DB::connection($this->db)->table($this->table)->truncate();
        DB::connection($this->db)->table($this->table)->insert([
            [
                'hallid' => 1,
                'account' => $_oFaker->firstName,
                'nickname' => 'test1',
                'password' => Hash::make('asd123'),
                'mobile' => '09'.$this->randomNum(8),
                'email' => $_oFaker->email,
                'memo' => $_oFaker->sentence(rand(10,50)),
                'token_active' => 1,
                'active' => 1,
            ],
            [
                'hallid' => 1,
                'account' => $_oFaker->firstName,
                'nickname' => 'test2',
                'password' => Hash::make('asd123'),
                'mobile' => '09'.$this->randomNum(8),
                'email' => $_oFaker->email,
                'memo' => $_oFaker->sentence(rand(10,50)),
                'token_active' => 0,
                'active' => 1,
            ],
            [
                'hallid' => 1,
                'account' => $_oFaker->firstName,
                'nickname' => 'test3',
                'password' => Hash::make('asd123'),
                'mobile' => '09'.$this->randomNum(8),
                'email' => $_oFaker->email,
                'memo' => null,
                'token_active' => 1,
                'active' => -1,
            ],
            [
                'hallid' => 2,
                'account' => $_oFaker->firstName,
                'nickname' => 'test4',
                'password' => Hash::make('asd123'),
                'mobile' => '09'.$this->randomNum(8),
                'email' => $_oFaker->email,
                'memo' => null,
                'token_active' => 1,
                'active' => 1,
            ],
            [
                'hallid' => 2,
                'account' => $_oFaker->firstName,
                'nickname' => 'test5',
                'password' => Hash::make('asd123'),
                'mobile' => '09'.$this->randomNum(8),
                'email' => $_oFaker->email,
                'memo' => $_oFaker->sentence(rand(10,50)),
                'token_active' => 0,
                'active' => -1,
            ],
        ]);
    }

    private function randomNum($_iLength = 8) {
        $sCharacters = '0123456789';
        $sCharactersLength = strlen($sCharacters);
        $sRandomString = '';
        for ($i = 0; $i < $_iLength; $i++) {
            $sRandomString .= $sCharacters[rand(0, $sCharactersLength - 1)];
        }
        return $sRandomString;
    }
}
