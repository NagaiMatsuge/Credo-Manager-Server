<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('step_id');
            $table->boolean('finished')->default(false);
            $table->boolean('approved')->default(false)->comment('Only can be approved by Admin');
            $table->timestamps();

            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');
        });

        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->unsignedBigInteger('task_id');
            $table->integer('time')->comment('number of minites');
            $table->boolean('unlim')->comment('is time fixed');
            $table->boolean('tick')->default(false)->comment('is time ticking');
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'task_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
