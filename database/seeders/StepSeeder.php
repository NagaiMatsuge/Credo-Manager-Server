<?php

namespace Database\Seeders;

use App\Models\DbAccess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $steps  = [];
        $steps[] = [
            'id' => 1,
            'title' => 'Создать модель проекта на фигме',
            'project_id' => 1,
            'price' => 2000,
            'debt' => 1500,
            'currency_id' => 1,
            'payment_type' => 1,
        ];
        $steps[] = [
            'id' => 2,
            'title' => 'Наверстать весь проект',
            'project_id' => 1,
            'price' => 1500,
            'debt' => 1300,
            'currency_id' => 1,
            'payment_type' => 1,
        ];
        $steps[] = [
            'id' => 3,
            'title' => 'Подготовить API для фронтенда',
            'project_id' => 1,
            'price' => 1800,
            'debt' => 1500,
            'currency_id' => 1,
            'payment_type' => 1,
        ];
        $steps[] = [
            'id' => 4,
            'title' => 'Создать модель проекта на фигме',
            'project_id' => 2,
            'price' => 2200,
            'debt' => 900,
            'currency_id' => 1,
            'payment_type' => 1,
        ];
        $steps[] = [
            'id' => 5,
            'title' => 'Подготовить фронт',
            'project_id' => 2,
            'price' => 2000,
            'debt' => 1200,
            'currency_id' => 1,
            'payment_type' => 1,
        ];

        DB::table('steps')->insert($steps);
    }
}
