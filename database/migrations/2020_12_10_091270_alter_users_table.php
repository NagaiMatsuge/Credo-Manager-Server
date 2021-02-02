<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
            $table->unsignedBigInteger('active_task_id')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->time('work_start_time', 0);
            $table->time('work_end_time', 0);
            $table->uuid('manager_id')->nullable();
            $table->time('pause_start_time', 0)->comment('when break time starts');
            $table->time('pause_end_time', 0)->comment('when break time ends');
            $table->json('working_days')->comment('[1,2,3] -> monday, tuesday, wednesday');
            $table->text('photo')->nullable();
            $table->string('color');
            $table->enum('theme', config('params.themes'))->default(config('params.themes')['1']);
            $table->foreign('active_task_id')->references('id')->on('task_user');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->unsignedBigInteger('back_up_active_task_id')->nullable();
            $table->foreign('back_up_active_task_id')->references('id')->on('task_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
