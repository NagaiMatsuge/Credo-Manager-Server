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
            'created_at' => now()
        ];
        $servers[] = [
            'title' => 'Time Manager',
            'host' => 'time_manager.credo/',
            'created_at' => now()
        ];

        DB::table("servers")->insert($servers);
    }
}
