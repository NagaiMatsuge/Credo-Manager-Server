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
            'created_at' => now()
        ];
        $taskUser[] = [
            'user_id' => $userIds[0],
            'task_id' => 3,
            'active' => false,
            'created_at' => now()
        ];
        $taskUser[] = [
            'user_id' => $userIds[1],
            'task_id' => 4,
            'active' => true,
            'created_at' => now()
        ];
        $taskUser[] = [
            'user_id' => $userIds[1],
            'task_id' => 5,
            'active' => false,
            'created_at' => now()
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 7,
            'active' => true,
            'created_at' => now()
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 8,
            'active' => false,
            'created_at' => now()
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 2,
            'active' => false,
            'created_at' => now()
        ];
        DB::table("task_user")->insert($taskUser);
    }
}
