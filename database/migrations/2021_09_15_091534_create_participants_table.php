<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->text('profile')->nullable();
            $table->string('sessid')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('geo')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('source_id')->nullable();
            $table->string('source_value')->nullable();
            $table->string('source_campaign')->nullable();
            $table->foreignId('project_id');
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
        Schema::dropIfExists('participants');
    }
}
