<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEcpayDomainTable extends Migration
{
    private $table = 'ecpay_domain';
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
            $table->string('url', 100)->unique()->comment('綠界通知Domain');
            $table->unsignedTinyInteger('active')->default(1)->comment('狀態(1:啟用,2:停用)');
            $table->unsignedTinyInteger('sort')->default(0)->comment('排序(由數字小到大由上往下排列顯示)');
            $table->timestamps();
        });
        DB::connection($this->db)->statement("ALTER TABLE `" . $this->table . "` COMMENT '綠界通知Domain設定'");
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
