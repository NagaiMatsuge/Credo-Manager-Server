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
            $table->time('work_start_time', 0);
            $table->time('work_end_time', 0);
            $table->integer('manager_id')->nullable();
            $table->time('pause_start_time', 0)->comment('when break time starts');
            $table->time('pause_end_time', 0)->comment('when break time ends');
            $table->json('working_days')->comment('[1,2,3] -> monday, tuesday, wednesday');
            $table->boolean('developer')->comment("If the user is developer");
            $table->text('photo')->nullable();
            $table->string('color');
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
