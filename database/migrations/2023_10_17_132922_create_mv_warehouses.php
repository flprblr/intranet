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
        Schema::create('mv_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('id_warehouse');
            $table->string('description');
            $table->string('vkorg', 4);
            $table->string('werks', 4);
            $table->string('lgort', 4);
            $table->string('vtweg', 2);
            $table->string('des_sovos');
            $table->string('logo');
            $table->bigInteger('company_id');
            $table->boolean('active');
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
        Schema::dropIfExists('mv_warehouses');
    }
};
