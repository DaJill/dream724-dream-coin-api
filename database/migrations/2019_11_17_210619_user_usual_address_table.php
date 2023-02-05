<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserUsualAddressTable extends Migration
{
    private $table = 'user_usual_address';
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
                $table->bigIncrements('id')->unsigned()->comment('流水號');
                $table->bigInteger('account_id')->unsigned()->comment('使用者ID');
                $table->string('name', 100)->nullable()->comment('寄件人名稱');
                $table->string('mobile', 20)->comment('訂單聯絡電話');
                $table->string('address', 500)->comment('常用地址');
                $table->string('convenience_store', 100)->comment('超商');
                $table->index('account_id');
                $table->index('address');
            }
        );
        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '使用者常用地址'");
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
