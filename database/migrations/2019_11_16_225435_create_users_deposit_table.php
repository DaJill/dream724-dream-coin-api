<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersDepositTable extends Migration
{
    private $table = 'users_deposit';
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
            $table->unsignedTinyInteger('type')->default(1)->comment('類型(1：存款管理，2：入點管理)');
            $table->string('no', 50)->unique()->comment('存款編號');
            $table->unsignedBigInteger('user_id')->comment('會員ID');
            $table->unsignedSmallInteger('payway_id')->comment('交易付款ID');
            $table->unsignedSmallInteger('deposit_account_id')->default(0)->comment('存款帳戶管理ID');
            $table->json('payway_info')->nullable()->comment('會員繳費資訊');
            $table->json('deposit_info')->nullable()->comment('會員選擇存款帳戶資訊');
            $table->unsignedDecimal('credit', 15, 4)->unsigned()->default(0)->comment('儲值金額');
            $table->unsignedTinyInteger('payway_status')->default(2)->comment('付款狀態(1：付款成功，2：待付款，3：付款失敗)');
            $table->unsignedTinyInteger('status')->default(2)->comment('單據狀態(1：已完成，2：未處理，3：已取消)');
            $table->unsignedSmallInteger('review_admin_id')->default(0)->comment('審核帳號ID');
            $table->dateTime('review_at')->nullable()->comment('審核時間');
            $table->string('reason', 50)->default('')->comment('取消原因');
            $table->date('deposit_at')->index()->comment('存款日期');
            $table->string('ip', 46)->default('')->comment('IP');
            $table->json('third_party_info')->nullable()->comment('第三方支付資訊');
            $table->timestamps();

            $table->index(['no'], $this->table . '_idx_1');
            $table->index(['status', 'created_at'], $this->table . '_idx_2');
            $table->index(['no', 'user_id', 'created_at'], $this->table . '_idx_3');
            $table->index(['payway_id', 'user_id', 'status', 'deposit_at'], $this->table . '_idx_4');
            $table->index(['payway_id', 'user_id', 'status', 'created_at'], $this->table . '_idx_5');
            $table->index(['payway_id', 'payway_status', 'status', 'created_at'], $this->table . '_idx_6');
            $table->index(['user_id'], $this->table . '_idx_7');
        });

        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '會員存款訂單'");
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
