<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = DB::table('users')->select('id')->pluck('id');
        $taskUser = [];
        $taskUser[] = [
            'user_id' => $userIds[0],
            'task_id' => 1,
            'created_at' => now(),
            'time' => 45,
            'type' => 1,
            'deadline' => null
        ];
        $taskUser[] = [
            'user_id' => $userIds[0],
            'task_id' => 3,
            'created_at' => now(),
            'time' => 456,
            'type' => 2,
            'deadline' => '2021-01-27'
        ];
        $taskUser[] = [
            'user_id' => $userIds[1],
            'task_id' => 4,
            'created_at' => now(),
            'time' => 375,
            'type' => 1,
            'deadline' => null

        ];
        $taskUser[] = [
            'user_id' => $userIds[1],
            'task_id' => 5,
            'created_at' => now(),
            'time' => 360,
            'type' => 1,
            'deadline' => null

        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 7,
            'created_at' => now(),
            'time' => 700,
            'type' => 1,
            'deadline' => null

        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 8,
            'created_at' => now(),
            'type' => 3,
            'deadline' => null,
            'time' => 0,

        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 2,
            'created_at' => now(),
            'type' => 3,
            'deadline' => null,
            'time' => 0,
        ];
        DB::table("task_user")->insert($taskUser);
    }
}
