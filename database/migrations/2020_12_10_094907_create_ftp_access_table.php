<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFtpAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftp_acess', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('server_id');
            $table->string('title');
            $table->string('host');
            $table->string('login');
            $table->text('password');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('ftp_acess');
    }
}
