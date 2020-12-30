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
            'active' => true,
            'created_at' => now(),
            'time' => 45,
            'tick' => true,
            'unlim' => true
        ];
        $taskUser[] = [
            'user_id' => $userIds[0],
            'task_id' => 3,
            'active' => false,
            'created_at' => now(),
            'time' => 456,
            'tick' => false,
            'unlim' => true
        ];
        $taskUser[] = [
            'user_id' => $userIds[1],
            'task_id' => 4,
            'active' => true,
            'created_at' => now(),
            'time' => 375,
            'tick' => true,
            'unlim' => false
        ];
        $taskUser[] = [
            'user_id' => $userIds[1],
            'task_id' => 5,
            'active' => false,
            'created_at' => now(),
            'time' => 360,
            'tick' => false,
            'unlim' => true
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 7,
            'active' => true,
            'created_at' => now(),
            'time' => 700,
            'tick' => false,
            'unlim' => true
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 8,
            'active' => false,
            'created_at' => now(),
            'time' => 455,
            'tick' => false,
            'unlim' => false
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 2,
            'active' => false,
            'created_at' => now(),
            'time' => 600,
            'tick' => false,
            'unlim' => true
        ];
        DB::table("task_user")->insert($taskUser);
    }
}
