<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $messages = [];
        $user_ids = DB::table('users')->get()->pluck('id');
        $user_ids_count = count($user_ids);

        $task_ids = DB::table('tasks')->get()->pluck('id');
        $task_ids_count = count($task_ids);

        for ($i = 0; $i < 100; $i++) {
            $messages[] = [
                'user_id' => $user_ids[rand(0, $user_ids_count - 1)],
                'text' => Str::random(60),
                'task_id' => $task_ids[rand(0, $task_ids_count - 1)],
                'name' => Str::random(20),
                'created_at' => now()
            ];
        }
        DB::table('messages')->insert($messages);
    }
}
