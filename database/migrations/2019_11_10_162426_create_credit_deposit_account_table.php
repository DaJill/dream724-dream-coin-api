<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditDepositAccountTable extends Migration
{
    private $table = 'credit_deposit_account';
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
            $table->unsignedSmallInteger('bank_id')->comment('銀行資料id');
            $table->string('code', 3)->default('')->comment('銀行代碼');
            $table->string('branch', 30)->default('')->comment('分行名稱');
            $table->string('account', 20)->default('')->comment('銀行帳號');
            $table->string('name', 20)->default('')->comment('戶名');
            $table->unsignedTinyInteger('active')->default(2)->comment('狀態(1:啟用,2:停用)');
            $table->unsignedDecimal('debit', 15, 4)->unsigned()->default(0)->comment('審核存點');
            $table->unsignedDecimal('credit', 15, 4)->unsigned()->default(0)->comment('入帳存點');
            $table->timestamps();

            $table->index(['active'], $this->table . '_idx_1');
            $table->index(['code', 'account'], $this->table . '_idx_2');
        });

        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '存款帳戶管理'");
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
