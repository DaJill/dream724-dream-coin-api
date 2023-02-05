<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEbpaySettingTable extends Migration
{
    private $table = 'ebpay_setting';
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
            $table->smallIncrements('id')->comment('PK');
            $table->string('merchant_id', 20)->comment('特店編號');
            $table->string('hash_key', 50)->comment('HashKey');
            $table->string('hash_iv', 30)->comment('HashIV');
            $table->string('api_url', 100)->comment('藍新API串接網址');
            $table->string('notice_url', 100)->comment('藍新背景通知網址');
            $table->unsignedTinyInteger('active')->default(2)->comment('狀態(1:啟用,2:停用)');
            $table->timestamps();
            $table->unique(['merchant_id', 'hash_key', 'hash_iv']);
            $table->index(['active'], $this->table . '_idx_1');
        });
        DB::connection($this->db)->statement("ALTER TABLE `" . $this->table . "` COMMENT '藍新金流設定'");
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
