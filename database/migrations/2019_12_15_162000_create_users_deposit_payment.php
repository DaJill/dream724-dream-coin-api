<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersDepositPayment extends Migration
{
    private $table = 'users_deposit_payment';
    private $db = 'Product';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->db)->create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK');
            $table->unsignedInteger('order_id')->comment('訂單編號');
            $table->unsignedSmallInteger('payway_id')->comment('交易付款ID');
            $table->unsignedSmallInteger('deposit_account_id')->default(0)->comment('存款帳戶管理ID');
            $table->json('payway_info')->nullable()->comment('會員繳費資訊');
            $table->json('deposit_info')->nullable()->comment('會員選擇存款帳戶資訊');
            $table->unsignedDecimal('credit', 15, 4)->unsigned()->default(0)->comment('儲值金額');
            $table->json('payment_info')->nullable()->comment('第三方支付資訊');
            $table->timestamps();

            $table->foreign('order_id', $this->table.'_ibfk_1')
                ->references('order_id')->on('product_order_list')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index(['order_id'], $this->table . '_idx_1');
            $table->index(['order_id', 'payway_id','deposit_account_id'], $this->table . '_idx_2');
        });

        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '會員存款訂單 - 銀行資訊'");
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
