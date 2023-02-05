<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEcpaySettingTable extends Migration
{
    private $table = 'ecpay_setting';
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
            $table->string('hash_key', 30)->comment('HashKey');
            $table->string('hash_iv', 30)->comment('HashIV');
            $table->string('api_url', 100)->comment('綠界API串接網址');
            $table->unsignedSmallInteger('ecpay_domain_id')->comment('綠界通知DomainID');
            $table->unsignedTinyInteger('active')->default(2)->comment('狀態(1:啟用,2:停用)');
            $table->timestamps();
            $table->unique(['merchant_id', 'hash_key', 'hash_iv']);
            $table->index(['active'], $this->table . '_idx_1');
        });
        DB::connection($this->db)->statement("ALTER TABLE `" . $this->table . "` COMMENT '綠界金流設定'");
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
