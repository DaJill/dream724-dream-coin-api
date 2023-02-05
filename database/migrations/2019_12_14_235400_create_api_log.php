<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiLog extends Migration
{
    private $table = 'api_log';
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
            $table->string('method', 50)->default('')->comment('方法名稱');
            $table->json('content')->nullable()->comment('content');
            $table->timestamps();

            $table->index(['method'], $this->table . '_idx_1');
        });

        DB::connection($this->db)->statement("ALTER TABLE `".$this->table."` COMMENT 'LOG資訊'");
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
