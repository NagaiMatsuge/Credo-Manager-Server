<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = DB::table('users as t1')->leftJoin('model_has_roles as t2', 't1.id', '=', 't2.model_uuid')->leftJoin('roles as t3', 't2.role_id', '=', 't3.id')->where('t3.name', 'Admin')->select('t1.id')->first();
        $notifications = [];
        $notifications[] = [
            'user_id' => $admin->id,
            'text' => "Hello world! This is the first notification that was ever born in this software",
            'publish_date' => '2021-01-12',
            'created_at' => now()
        ];
        $notifications[] = [
            'user_id' => $admin->id,
            'text' => "Okaaay fellas, you have to finish the project by saturday!",
            'publish_date' => '2021-01-25',
            'created_at' => now()
        ];
        $notifications[] = [
            'user_id' => $admin->id,
            'text' => "Every employer should submit their progress chart by the next two weeks",
            'publish_date' => '2021-01-16',
            'created_at' => now()
        ];

        DB::table("notifications")->insert($notifications);
    }
}
