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
        Schema::create('mv_orders', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 25);
            $table->string('order_number');
            $table->string('invoice_number', 255)->nullable();
            $table->string('invoice_url', 255)->nullable();
            $table->date('date_created');
            $table->integer('net');
            $table->integer('tax');
            $table->integer('total');
            $table->string('billing_name', 255);
            $table->string('billing_last_name', 255);
            $table->string('billing_rut', 255);
            $table->string('billing_phone', 255);
            $table->string('billing_email', 255);
            $table->string('billing_state', 255);
            $table->string('billing_city', 255);
            $table->string('billing_address_1', 255);
            $table->string('payment_status');
            $table->bigInteger('order_status_id');
            $table->bigInteger('marketplace_id');
            $table->bigInteger('store_id');
            $table->bigInteger('id_warehouse');
            $table->string('checkout_id');
            $table->string('client_id');
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
        Schema::dropIfExists('mv_orders');
    }
};
