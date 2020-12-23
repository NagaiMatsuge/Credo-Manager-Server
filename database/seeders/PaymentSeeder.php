<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payments = [];
        $payments[] = [
            'comment' => 'First Payment',
            'payment_date' => '2020-12-09 12:00:16',
            'step_id' => 1,
            'currency_id' => 1,
            'amount' => 1456,
            'payment_type' => 1
        ];
        $payments[] = [
            'comment' => 'Second Payment',
            'payment_date' => '2020-12-10 12:00:16',
            'step_id' => 1,
            'currency_id' => 2,
            'amount' => 144,
            'payment_type' => 2
        ];
        $payments[] = [
            'comment' => 'Third Payment',
            'payment_date' => '2020-11-09 12:00:16',
            'step_id' => 1,
            'currency_id' => 3,
            'amount' => 195655,
            'payment_type' => 3
        ];
        $payments[] = [
            'comment' => 'Forth Payment',
            'payment_date' => '2019-11-15 12:00:16',
            'step_id' => 4,
            'currency_id' => 2,
            'amount' => 956,
            'payment_type' => 3
        ];
        $payments[] = [
            'comment' => 'Some random payment',
            'payment_date' => '2020-07-09 12:00:14',
            'step_id' => 4,
            'currency_id' => 1,
            'amount' => 1456,
            'payment_type' => 3
        ];
        $payments[] = [
            'comment' => 'Some more payment',
            'payment_date' => '2020-11-05 10:00:00',
            'step_id' => 4,
            'currency_id' => 1,
            'amount' => 187564,
            'payment_type' => 2
        ];
        DB::table('payments')->insert($payments);
    }
}
