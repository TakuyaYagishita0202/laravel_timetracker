<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('memo')->nullable();
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('started_at');
            $table->timestamp('stopped_at')->default(null)->nullable();
            $table->timestamps();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->string('category_color')->nullable();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timers');
    }
}
