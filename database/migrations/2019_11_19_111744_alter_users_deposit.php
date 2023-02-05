<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersDeposit extends Migration
{
    private $table = 'users_deposit';
    private $db = 'User';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection($this->db)->statement("ALTER TABLE `" . $this->table .
            "`MODIFY COLUMN payway_status tinyint(3) unsigned NOT NULL DEFAULT '1' 
            COMMENT '付款狀態(1：待付款，2：付款成功，3：付款失敗)'");

        DB::connection($this->db)->statement("ALTER TABLE `" . $this->table .
            "`MODIFY COLUMN status tinyint(3) unsigned NOT NULL DEFAULT '1' 
            COMMENT '單據狀態(1：未處理，2：已完成，3：已取消)'");
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
