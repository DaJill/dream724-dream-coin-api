<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUserTable extends Migration
{
    private $table = 'admin_user';

    public $connection = 'User';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
            $table->smallIncrements('id')->comment('PK');
            $table->string('account', 12)->comment('帳號');
            $table->string('password', 60)->default('')->comment('密碼');
            $table->string('name', 10)->default('')->comment('暱稱');
            $table->longText('token')->comment('登入 Token');
            $table->unsignedTinyInteger('active')->default(1)->comment('狀態(1:啟用,2:停用)');
            $table->dateTime('login_at')->nullable()->comment('最後登入時間');
            $table->timestamps();

            $table->unique(['account'], 'uk_' . $this->table . '_1');
            $table->index(['account', 'active'], 'idx_' . $this->table . '_1');
        });

        DB::connection($this->connection)->statement("ALTER TABLE `".$this->table."` COMMENT '後台帳號資料'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists($this->table);
    }
}
