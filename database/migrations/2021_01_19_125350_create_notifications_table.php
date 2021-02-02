<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->text('text');
            $table->dateTime('publish_date');
            $table->enum('type', [1, 2])->default(1)->comment('1 - ordinary, 2 - only-to-user-not-admin');
            $table->integer('job_number')->nullable()->comment("Job number at AT command linux");
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('notification_user', function (Blueprint $table) {
            $table->id();
            $table->uuid('to_user');
            $table->unsignedBigInteger('notification_id');
            $table->boolean('read')->default(0);

            $table->foreign('to_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
