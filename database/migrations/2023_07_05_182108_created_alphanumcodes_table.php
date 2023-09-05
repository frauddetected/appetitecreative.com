<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedAlphanumcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alphanumcodes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('title')->nullable();
            $table->boolean('burned')->default(false);
            $table->foreignId('project_id')->index();
            $table->foreignId('source_id')->nullable();
            $table->string('source_value')->nullable();
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
        Schema::dropIfExists('alphanumcodes');
    }
}
