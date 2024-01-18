<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('FKDAT')->index();
            $table->string('BUKRS', 4)->index();
            $table->string('VKORG', 4)->index();
            $table->string('VTWEG', 2)->index();
            $table->string('NAME1', 255)->index();
            $table->integer('SALE');
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
        Schema::dropIfExists('lastyearday');
    }
};
