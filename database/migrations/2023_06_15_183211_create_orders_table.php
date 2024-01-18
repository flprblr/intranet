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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 25);
            $table->bigInteger('order_number');
            $table->string('invoice_number', 255)->nullable();
            $table->string('invoice_number_rev', 255)->nullable();
            $table->string('invoice_url', 255)->nullable();
            $table->string('invoice_url_rev', 255)->nullable();
            $table->date('date_created');
            $table->date('date_modified');
            $table->string('discount_code', 255)->nullable();
            $table->string('discount_type', 255)->nullable();
            $table->integer('discount_amount')->nullable();
            $table->integer('discount_total')->nullable();
            $table->integer('shipping_total');
            $table->integer('net');
            $table->integer('tax');
            $table->integer('total');
            $table->string('billing_first_name', 255);
            $table->string('billing_last_name', 255);
            $table->string('billing_rut', 255);
            $table->string('billing_company', 255)->nullable();
            $table->string('billing_phone', 255);
            $table->string('billing_email', 255);
            $table->string('billing_country', 255);
            $table->string('billing_state', 255);
            $table->string('billing_city', 255);
            $table->string('billing_address_1', 255);
            $table->bigInteger('order_status_id');
            $table->bigInteger('payment_id');
            $table->bigInteger('ecommerce_id');
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
        Schema::dropIfExists('orders');
    }
};
