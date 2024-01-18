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
        Schema::create('ecommerces', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 25);
            $table->string('url', 50);
            $table->string('api_key', 43);
            $table->string('api_secret', 43);
            $table->boolean('status');
            $table->string('logo', 25);
            $table->string('VKORG', 4); // ORGANIZACION DE VENTA
            $table->string('WERKS', 4); // CENTRO
            $table->string('LGORT', 4); // ALMACEN
            $table->string('AUART', 4); // CLASE DE PEDIDO
            $table->string('FKART', 4); // CLASE DE FACTURACION
            $table->foreignId('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('ecommerces');
    }
};
