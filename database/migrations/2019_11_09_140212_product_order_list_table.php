<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductOrderListTable extends Migration
{
    private $table = 'product_order_list';
    private $db = 'Product';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->db)->create($this->table, function (Blueprint $table) {
            $table->increments('order_id')->increments()->unsigned()->comment('訂單ID');
            $table->string('code', 8)->comment('訂單碼');
            $table->Integer('product_id')->comment('募款ID');
            $table->Integer('account_id')->unsigned()->comment('訂購人ID');
            $table->string('name', 100)->nullable()->comment('寄件人名稱');
            $table->string('mobile', 20)->comment('訂單聯絡電話');
            $table->tinyInteger('payway_id')->unsigned()->default(0)->comment('交易付款ID');
            $table->string('address', 500)->nullable()->comment('送貨地址');
            $table->dateTime('order_datetime')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('申請時間');//Integer('order_time')->unsigned()->default(0)->comment('申請時間');
            $table->dateTime('deadline')->default(DB::raw('CURRENT_TIMESTAMP '))->comment('截止時間');
            $table->tinyInteger('pay_status')->unsigned()->default(0)->comment('付款狀態 0未付款, 1已付款');
            $table->tinyInteger('order_status')->default(0)->comment('訂單狀態 0審核中, 1訂單通過, -1訂單取消');
            $table->tinyInteger('delivery_status')->default(0)->comment('出貨狀態 0待出貨, 1已出貨, -1出貨取消');
            $table->Integer('modify_admin_id')->nullable()->comment('最後異動管理者ID');
            $table->string('reason', 100)->nullable()->comment('訂單取消原因 或 出貨取消原因');
            $table->text('note')->nullable()->comment('會員備註');
            $table->Integer('price_total')->unsigned()->default(0)->comment('總金額');
            $table->string('callback_code', 100)->nullable()->comment('外部回call碼');
            $table->dateTime('modify_datetime')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新時間');
        });
    
        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '訂單資訊'");
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
