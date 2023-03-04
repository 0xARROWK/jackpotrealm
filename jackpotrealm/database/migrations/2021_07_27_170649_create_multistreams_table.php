<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultistreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multistreams', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('url', 100);
            $table->string('key', 100);
            $table->tinyInteger('status');
            $table->integer('pid')->default(null)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('multistreams');
    }
}
