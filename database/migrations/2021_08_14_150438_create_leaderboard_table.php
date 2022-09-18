<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function ups()
    {
        Schema::create('leaderboard', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('participant')->nullable();
            $table->integer('score');
            $table->text('details')->nullable();
            $table->string('origin')->nullable();
            $table->string('origin_value')->nullable();
            $table->string('source_id')->nullable();
            $table->string('source_value')->nullable();
            $table->string('source_campaign')->nullable();
            $table->string('sessid')->nullable();
            $table->foreignId('project_id')->index();
            $table->integer('prize_id')->nullable();
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
        Schema::dropIfExists('leaderboard');
    }
}
