<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tasks = [];
        $tasks[] = [
            'title' => 'Наверстать страницу проекты',
            'project_id' => 2,
            'price' => 200,
            'debt' => 150,
            'currency_id' => 1,
            'payment_type' => 1,
            'payment_date' => now()->addMonth(3),
            'finished' => 1,
            'approved' => 1,
            'deadline' => now()->addMonth(3)
        ];
        $tasks[] = [
            'title' => 'Пагинатсия на сайте',
            'project_id' => 2,
            'price' => 100,
            'debt' => 75,
            'currency_id' => 1,
            'payment_type' => 1,
            'payment_date' => now()->addMonth(2),
            'finished' => 1,
            'approved' => 1,
            'deadline' => now()->addMonth(2)
        ];
        $tasks[] = [
            'title' => 'Добавить цвета для проекта',
            'project_id' => 2,
            'price' => 50,
            'debt' => 35,
            'currency_id' => 1,
            'payment_type' => 1,
            'payment_date' => now()->addMonth(1),
            'finished' => 0,
            'approved' => 0,
            'deadline' => now()->addMonth(1)
        ];
        $tasks[] = [
            'title' => 'Сделать сайт адаптивным',
            'project_id' => 1,
            'price' => 300,
            'debt' => 275,
            'currency_id' => 1,
            'payment_type' => 2,
            'payment_date' => now()->addMonth(4)->addDays(2),
            'finished' => 1,
            'approved' => 1,
            'deadline' => now()->addMonth(4)->addDays(3)
        ];
        $tasks[] = [
            'title' => 'Изменить цветь иконок',
            'project_id' => 1,
            'price' => 10,
            'debt' => 7,
            'currency_id' => 1,
            'payment_type' => 3,
            'payment_date' => now()->addDays(1),
            'finished' => 0,
            'approved' => 0,
            'deadline' => now()->addDays(3)
        ];

        DB::table('tasks')->insert($tasks);
    }
}
