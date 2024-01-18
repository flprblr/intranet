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
        Schema::create('mv_orders_payments', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255);
            $table->string('sap_id', 4);
            $table->string('pos_id');
            $table->integer('amount');
            $table->date('date');
            $table->bigInteger('order_id');
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
        Schema::dropIfExists('mv_orders_payments');
    }
};
