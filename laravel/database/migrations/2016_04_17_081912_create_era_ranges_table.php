<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEraRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('era_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('era_id')->unsigned();
            $table->string('range_code');
            $table->bigInteger('start_value');
            $table->bigInteger('end_value')->nullable();
            $table->bigInteger('start_display');
            $table->enum('direction', ['asc', 'desc']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('era_ranges');
    }
}
