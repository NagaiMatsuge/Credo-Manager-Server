<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = [];
        $projects[] = [
            'id' => 1,
            'title' => 'Uzexpress',
            'description' => 'Collaboration of markets in one central place. Marketing and other stuff.',
            'color' => config('params.colors')[rand(0, 2)],
            'deadline' => now()->addMonth(2)
        ];
        $projects[] = [
            'id' => 2,
            'title' => 'Time Manager',
            'description' => 'Time manager application for credo studio',
            'color' => config('params.colors')[rand(0, 2)],
            'deadline' => now()->addMonth(3)
        ];

        DB::table('projects')->insert($projects);
    }
}
