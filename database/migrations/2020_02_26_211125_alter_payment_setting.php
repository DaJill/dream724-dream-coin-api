<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentSetting extends Migration
{
    private $table = 'payment_setting';
    private $db = 'User';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection($this->db)->statement("ALTER TABLE `" . $this->table .
            "`MODIFY COLUMN type tinyint(3) unsigned NOT NULL DEFAULT '1' 
            COMMENT '類型(1：綠界，2：藍新)'");
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
