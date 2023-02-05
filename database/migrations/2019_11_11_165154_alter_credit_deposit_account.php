<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCreditDepositAccount extends Migration
{
    private $table = 'credit_deposit_account';
    private $db = 'User';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::connection($this->db)->hasColumns($this->table, ['bank_id','account'])) {
            Schema::connection($this->db)->table($this->table, function (Blueprint $table) {
                $table->unique(['bank_id','account']);
            });
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
