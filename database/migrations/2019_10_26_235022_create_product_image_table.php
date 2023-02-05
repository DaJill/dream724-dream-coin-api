<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductImageTable extends Migration
{
    private $table = 'product_image';
    private $db = 'Product';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->db)->create($this->table, function (Blueprint $table) {
            $table->Integer('id')->unsigned()->comment('商品ID');
            $table->tinyInteger('sort')->unsigned()->default(0)->comment('圖片排序');
            $table->string('image_path', 500)->nullable()->comment('商品圖片');
            $table->primary(['id', 'sort']);
        });

        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT '商品圖片'");
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
