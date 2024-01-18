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
        Schema::create('mv_orders_details', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 255);
            $table->string('description', 255);
            $table->integer('unit_price');
            $table->integer('quantity');
            $table->integer('subtotal');
            $table->integer('final_price');
            $table->integer('discount');
            $table->integer('total');
            $table->bigInteger('order_id');
            $table->bigInteger('id_warehouse');
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
        Schema::dropIfExists('mv_orders_details');
    }
};
