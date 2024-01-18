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
        Schema::create('ext_guarantees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ext_customer')->nullable();

            $table->string('xblnr')->nullable();
            $table->string('sold_by')->nullable();
            $table->string('sellername')->nullable();
            $table->string('sold_date')->nullable();
            $table->string('matnr')->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->string('serial')->nullable();


            // GEXT

            $table->string('xblnr_gext')->nullable();
            $table->string('matnr_gext')->nullable();
            $table->string('description_gext')->nullable();
            $table->integer('valor')->nullable();



            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            $table->text('comment')->nullable();
            $table->boolean('active')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

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
        Schema::dropIfExists('ext_guarantees');
    }
};
