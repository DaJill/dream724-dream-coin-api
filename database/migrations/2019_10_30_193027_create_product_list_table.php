<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductListTable extends Migration
{
    private $table = 'product_list';
    private $db = 'Product';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->db)->create($this->table, function (Blueprint $table) {
            $table->increments('id')->increments()->unsigned()->comment('專案ID');
            $table->string('code', 8)->comment('專案碼');
            $table->string('name', 500)->nullable()->comment('專案名稱');
            $table->text('description')->nullable()->comment('專案說明');
            $table->Integer('price_target')->unsigned()->default(0)->comment('募資目標金額');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('狀態 0下架, 1上架');
            $table->date('end_date')->comment('募款結束時間');
            $table->dateTime('modify_datetime')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新時間');
        });
    
        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '募資專案列表'");
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
