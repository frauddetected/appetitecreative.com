<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('content');
            $table->text('content_secondary')->nullable();
            $table->string('image')->nullable();
            $table->string('tags')->nullable();
            $table->string('status')->default('draft');
            $table->integer('count')->default(0);
            
            $table->foreignId('project_id')->index();
            $table->foreignId('source_id')->nullable();
            $table->string('source_value')->nullable();

            $table->string('country')->nullable();
            $table->string('language')->nullable();
            
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
        Schema::dropIfExists('articles');
    }
}
