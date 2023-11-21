<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_action', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->json('values')->nullable();
            $table->string('source_id')->nullable();
            $table->string('source_value')->nullable();
            $table->string('source_campaign')->nullable();
            $table->string('sessid')->nullable();
            $table->text('details')->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('project_id')->index();
            $table->foreignId('user_id')->nullable();
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
        Schema::dropIfExists('log_action');
    }
}
