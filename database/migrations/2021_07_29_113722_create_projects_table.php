<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('name');
            $table->string('ucode')->unique();
            $table->string('controller')->nullable();
            $table->string('domain')->nullable();
            $table->string('api_token', 80)->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_test')->default(false);
            $table->integer('parent_id')->nullable();
            $table->text('details')->nullable();
            $table->timestamp('ends_at')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
