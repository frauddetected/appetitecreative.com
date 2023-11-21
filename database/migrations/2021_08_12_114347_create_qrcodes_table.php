<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qrcodes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('parent_id')->nullable();
            $table->string('keyword')->unique();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->text('details')->nullable();
            $table->integer('scans')->default(0);
            $table->boolean('is_unique')->default(false);
            $table->boolean('is_burn')->default(false);
            $table->foreignId('project_id')->index();
            $table->foreignId('source_id')->nullable();
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
        Schema::dropIfExists('qrcodes');
    }
}
