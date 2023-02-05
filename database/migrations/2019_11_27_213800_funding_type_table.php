<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FundingTypeTable extends Migration
{
    private $table = 'funding_type';
    private $db = 'Product';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->db)->create($this->table, function (Blueprint $table) {
            $table->Integer('id')->unsigned()->comment('專案ID');
            $table->tinyInteger('sort')->unsigned()->default(0)->comment('排序');
            $table->text('description')->nullable()->comment('內容摘要');
            $table->Integer('price')->unsigned()->default(0)->comment('回饋金額');
            $table->date('delivery_date')->comment('預計出貨日');
    
            $table->primary(['id', 'sort']);
        });

        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '募款設定'");
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
