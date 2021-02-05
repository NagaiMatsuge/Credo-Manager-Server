<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 'roles' [
        //     'Admin',
        //     'Manager',
        //     'Backend Developer',
        //     'Frontend Developer',
        //     'UI\UX Designer',
        // ]
        // $users[] = User::create([
        //     'name' => 'Ruslan',
        //     'email' => 'menrusamen19992@gmail.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'work_start_time' => '9:00',
        //     'work_end_time' => '18:00',
        //     'pause_start_time' => '13:00',
        //     'pause_end_time' => '14:00',
        //     'working_days' => [1, 2, 3, 4, 5],
        //     'color' => '#FC73AD',
        //     'role_id' => 2
        // ]);
        // $users[] = User::create([
        //     'name' => 'Bobur',
        //     'email' => 'komilovboburweb@gmail.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'work_start_time' => '9:00',
        //     'work_end_time' => '18:00',
        //     'pause_start_time' => '13:00',
        //     'pause_end_time' => '14:00',
        //     'working_days' => [1, 2, 3, 4, 5],
        //     'color' => '#FCB573',
        //     'role_id' => 3
        // ]);
        $users[] = User::create([
            'name' => 'Ruslan',
            'email' => 'Admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'work_start_time' => '9:00',
            'work_end_time' => '18:00',
            'pause_start_time' => '13:00',
            'pause_end_time' => '14:00',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#8F73FC',
            'role_id' => 1
        ]);
        // $users[] = User::create([
        //     'name' => 'Shoxrux',
        //     'email' => 'shohrukh.mamirov@mail.ru',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'work_start_time' => '9:00',
        //     'work_end_time' => '18:00',
        //     'pause_start_time' => '13:00',
        //     'pause_end_time' => '14:00',
        //     'working_days' => [1, 2, 3, 4, 5],
        //     'color' => '#3B5998',
        //     'role_id' => 2
        // ]);
    }
}
