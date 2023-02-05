<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentSetting extends Migration
{
    private $table = 'payment_setting';

    public $connection = 'User';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('type')->default(1)->comment('類型(1：綠界，2：1177pay)');
            $table->timestamps();
        });

        DB::connection($this->connection)->statement("ALTER TABLE `".$this->table."` COMMENT '第三方支付類型'");
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
