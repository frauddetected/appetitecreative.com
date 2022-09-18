<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function ups()
    {
        Schema::create('qrcode_params', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('project_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function downs()
    {
        Schema::dropIfExists('qrcode_params');
    }
}
