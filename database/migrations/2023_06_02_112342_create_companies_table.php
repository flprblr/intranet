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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('rut', 10);
            $table->string('city', 100);
            $table->string('commune', 100);
            $table->string('address', 255);
            $table->string('activity', 255);
            $table->string('acteco', 6);
            $table->string('sovos_user', 25);
            $table->string('sovos_password', 25);
            $table->string('server', 4);
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
        Schema::dropIfExists('companies');
    }
};
