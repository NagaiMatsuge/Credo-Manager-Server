<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_access', function (Blueprint $table) {
            $table->id();
            $table->string('db_name');
            $table->string('host')->default('localhost');
            $table->string('login');
            $table->text('password');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('server_id');
            $table->timestamps();

            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('db_access');
    }
}
