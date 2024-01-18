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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('store_number');
            $table->string('description');
            $table->string('werks')->nullabe();
            $table->string('extension');
            $table->string('phone_number');
            $table->string('gateway_ip');
            $table->string('router_ip');
            $table->string('phone_ip');
            $table->string('clock_ip');
            $table->string('clock_model');
            $table->string('wifi_password');
            $table->string('service_code');
            $table->string('address');
            $table->string('email');
            $table->boolean('condition');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('state_id');
            $table->timestamps();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
};
