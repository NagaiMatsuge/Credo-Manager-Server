<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->text('comment');
            $table->dateTime('payment_date');
            $table->unsignedBigInteger('step_id');
            $table->smallInteger('currency_id');
            $table->double('amount', 13, 3);
            $table->smallInteger('payment_type')->comment('1-Qiwi, 2-Wmp, 3-Wmz, 4-Yandex, 5-Bank');

            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
