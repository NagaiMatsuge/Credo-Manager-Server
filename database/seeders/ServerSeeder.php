<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $servers = [];
        $servers[] = [
            'title' => 'Uzexpress',
            'host' => 'Uzexpress.ru',
            'project_id' => 1,
            'created_at' => now()
        ];
        $servers[] = [
            'title' => 'Time Manager',
            'host' => 'time_manager.credo/',
            'project_id' => 2,
            'created_at' => now()
        ];

        DB::table("servers")->insert($servers);
    }
}
