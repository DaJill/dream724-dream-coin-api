<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersInfoTable extends Migration
{
    private $table = 'users_info';
    private $db = 'User';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->db)->create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK');
            $table->unsignedBigInteger('user_id')->comment('會員ID');
            $table->json('bank_1')->nullable()->comment('銀行1資訊');
            $table->json('bank_2')->nullable()->comment('銀行2資訊');
            $table->json('bank_3')->nullable()->comment('銀行3資訊');
            $table->json('bank_4')->nullable()->comment('銀行4資訊');
            $table->json('bank_5')->nullable()->comment('銀行5資訊');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `".$this->table."` COMMENT '會員銀行資訊'");
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
