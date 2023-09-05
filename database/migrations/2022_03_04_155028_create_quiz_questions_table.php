<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('if_correct')->nullable();
            $table->text('if_incorrect')->nullable();
            $table->boolean('is_always_correct')->default(false);
            $table->boolean('is_multi_answer')->default(false);
            $table->json('tags')->nullable();
            $table->string('source')->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->text('details')->nullable();
            $table->string('source_id')->nullable();
            $table->string('source_value')->nullable();
            $table->foreignId('project_id')->index();
            $table->foreignId('qrcode_id')->nullable();
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
        Schema::dropIfExists('quiz_questions');
    }
}
