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
        Schema::create('mv_access', function (Blueprint $table) {
            $table->id();
            $table->string('base_url');
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('code')->nullable();
            $table->string('merchant_id')->nullable();
            $table->text('token')->nullable();
            $table->dateTime('expires_token')->nullable();
            $table->string('token_refresh')->nullable();
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
        Schema::dropIfExists('mv_access');
    }
};
