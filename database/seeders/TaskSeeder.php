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
            'step_id' => 2,
            'deadline' => null,
            'time' => 234,
            'type' => 1
        ];
        $tasks[] = [
            'title' => 'Пагинатсия на сайте',
            'step_id' => 2,
            'deadline' => null,
            'time' => 234,
            'type' => 1
        ];
        $tasks[] = [
            'title' => 'Добавить цвета для проекта',
            'step_id' => 1,
            'deadline' => null,
            'time' => 234,
            'type' => 1,
        ];
        $tasks[] = [
            'title' => 'Сделать сайт адаптивным',
            'step_id' => 1,
            'deadline' => null,
            'time' => 234,
            'type' => 1,
        ];
        $tasks[] = [
            'title' => 'Изменить цветь иконок',
            'step_id' => 1,
            'deadline' => null,
            'time' => 234,
            'type' => 1,
        ];
        $tasks[] = [
            'title' => 'Процент оплаты нужны для проектов',
            'step_id' => 4,
            'deadline' => null,
            'time' => 234,
            'type' => 1,
        ];
        $tasks[] = [
            'title' => 'Настроить платежную систему',
            'step_id' => 4,
            'deadline' => null,
            'time' => 234,
            'type' => 1,
        ];
        $tasks[] = [
            'title' => 'У проектов должны быть иконки',
            'step_id' => 5,
            'deadline' => null,
            'time' => 234,
            'type' => 1,
        ];


        DB::table('tasks')->insert($tasks);
    }
}
