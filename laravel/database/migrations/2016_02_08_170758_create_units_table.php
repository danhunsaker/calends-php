<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendar_id')->unsigned();
            $table->string('singular_name');
            $table->string('plural_name');
            $table->bigInteger('scale_amount')->nullable();
            $table->boolean('scale_inverse');
            $table->integer('scale_to')->unsigned();
            $table->boolean('uses_zero');
            $table->bigInteger('unix_epoch')->nullable();
            $table->boolean('is_auxiliary');
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
        Schema::drop('units');
    }
}
