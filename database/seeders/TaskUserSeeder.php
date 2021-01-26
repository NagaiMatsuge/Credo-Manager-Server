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
        ];
        $taskUser[] = [
            'user_id' => $userIds[0],
            'task_id' => 3,
        ];
        $taskUser[] = [
            'user_id' => $userIds[1],
            'task_id' => 4,
        ];
        $taskUser[] = [
            'user_id' => $userIds[1],
            'task_id' => 5,
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 7,
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 8,
        ];
        $taskUser[] = [
            'user_id' => $userIds[2],
            'task_id' => 2,
        ];
        DB::table("task_user")->insert($taskUser);
    }
}
