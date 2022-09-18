<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function ups()
    {
        Schema::create('sharing', function (Blueprint $table) {
            $table->id();
            $table->text('style');
            $table->text('messages');
            $table->integer('count')->default(0);
            $table->foreignId('project_id')->index();
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
        Schema::dropIfExists('sharing');
    }
}
