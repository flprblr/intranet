<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZappleTbCorreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zapple_tb_corre', function (Blueprint $table) {
            $table->id();  // Columna ID autoincremental
            
            $table->integer('correlativo')->nullable();
            $table->date('fecha');
            $table->string('tipo', 3);  // Suponiendo que tipo es una cadena de hasta 50 caracteres.
            
            $table->timestamps();  // Columnas 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zapple_tb_corre');
    }
}