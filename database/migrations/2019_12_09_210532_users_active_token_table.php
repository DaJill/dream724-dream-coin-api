<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersActiveTokenTable extends Migration
{
    private $table = 'users_active_token';
    private $db = 'User';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->db)->create(
            $this->table, function (Blueprint $table) {
                $table->bigInteger('id')->unsigned()->comment('使用者ID');
                $table->string('token', 60)->comment('激活碼');
                $table->tinyInteger('token_active')->default(0)->comment('激活 0未激活, 1已激活');
                $table->dateTime('create_datetime')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('建立時間');
                $table->dateTime('deadline')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('激活截止時間');
                $table->index('deadline');
                $table->primary(['token']);
            }
        );
        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '帳號驗證'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->db)->dropIfExists($this->table);
    }
}
