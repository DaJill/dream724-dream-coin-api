<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaywaysTable extends Migration
{
    private $table = 'payways';
    private $db = 'User';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->db)->create($this->table, function (Blueprint $table) {
            $table->smallIncrements('id')->comment('PK');
            $table->unsignedTinyInteger('type')->default(1)->comment('類型(1：超商付款，2：ATM，3：銀行卡)');
            $table->string('name', 20)->comment('付款名稱');
            $table->unsignedDecimal('upper_limit', 15, 4)->unsigned()->default(0)->comment('可儲值金額上限');
            $table->unsignedDecimal('lower_limit', 15, 4)->unsigned()->default(0)->comment('可儲值金額下限');
            $table->json('amount')->nullable()->comment('金流管理');
            $table->unsignedTinyInteger('active')->default(1)->comment('狀態(1：啟用，2：停用，3：敬請期待)');
            $table->timestamps();
        }
        );

        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '金流管理'");

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
