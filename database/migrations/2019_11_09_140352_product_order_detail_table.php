<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductOrderDetailTable extends Migration
{
    private $table = 'product_order_detail';
    private $db = 'Product';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //order_id	account_id	mobile	payment	address	order_time	deadline	order_status	delivery_status	price_total

        Schema::connection($this->db)->create($this->table, function (Blueprint $table) {
            $table->increments('detail_id')->increments()->unsigned()->comment('細單ID');
            $table->Integer('order_id')->unsigned()->comment('訂單ID');
            $table->Integer('product_id')->unsigned()->comment('商品ID');
            $table->string('name', 500)->nullable()->comment('商品名稱');
            $table->Integer('price')->unsigned()->default(0)->comment('商品單價');
            $table->Integer('count')->unsigned()->default(1)->comment('購買數量');
        });
    
        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '訂單明細'");
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
