<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskWatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_watchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_user_id');
            $table->dateTime('stopped_at')->nullable();
            $table->timestamps();

            $table->foreign('task_user_id')->references('id')->on('task_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_watchers');
    }
}
