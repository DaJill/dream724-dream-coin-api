<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    private $table = 'users';
    private $db = 'User';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::connection($this->db)->create(
            $this->table, function (Blueprint $table) {
                $table->bigIncrements('id')->unsigned()->comment('使用者ID');
                $table->Integer('hallid')->unsigned()->comment('平台ID');
                $table->string('account', 20)->comment('帳號');
                $table->string('nickname', 100)->nullable()->comment('暱稱');
                $table->string('password', 60)->comment('密碼');
                $table->string('mobile', 20)->nullable()->comment('手機');
                $table->string('email', 320)->comment('信箱');
                $table->text('memo')->nullable()->comment('備註');
                $table->tinyInteger('token_active')->default(0)->comment('激活 0未激活, 1已激活');
                $table->tinyInteger('active')->default(0)->comment('狀態 -1停用, 1啟用');
                $table->dateTime('create_datetime')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('建立時間');
                $table->dateTime('modify_datetime')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('資料更新時間');
                $table->longText('token')->nullable()->comment('登入 Token');
                $table->unique(['hallid', 'account']); //不同平台帳號可以相同
                $table->index('hallid');
                $table->index('account');
                $table->index('create_datetime');
                
            }
        );
        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '會員資料'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::connection($this->db)->dropIfExists($this->table);
    }
}
