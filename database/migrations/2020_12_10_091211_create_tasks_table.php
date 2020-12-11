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
            $table->unsignedBigInteger('project_id');
            $table->double('price', 13, 3);
            $table->smallInteger('currency_id');
            $table->smallInteger('payment_type')->comment('1-Qiwi, 2-Wmp, 3-Wmz, 4-Yandex, 5-Bank');
            $table->date('payment_date');
            $table->boolean('finished')->default(false);
            $table->boolean('approved')->default(false)->comment('Only can be approved by Admin');
            $table->time('time_left');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
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
