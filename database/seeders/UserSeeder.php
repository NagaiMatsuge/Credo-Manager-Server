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
        $users[] = User::create([
            'name' => 'Ruslan',
            'email' => 'menrusamen19992@gmail.com',
            'password' => Hash::make('password'),
            'work_start_time' => '9:9:9',
            'work_end_time' => '8:8:8',
            'pause_start_time' => '7:7:7',
            'pause_end_time' => '6:6:6',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#FC73AD'
        ]);
        $users[] = User::create([
            'name' => 'Bobur',
            'email' => 'komilovboburweb@gmail.com',
            'password' => Hash::make('password'),
            'work_start_time' => '9:9:9',
            'work_end_time' => '8:8:8',
            'pause_start_time' => '7:7:7',
            'pause_end_time' => '6:6:6',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#FC73AD'
        ]);
        $users[] = User::create([
            'name' => 'Shohrux',
            'email' => 'shohrukh.mamirov@gmail.com',
            'password' => Hash::make('password'),
            'work_start_time' => '9:9:9',
            'work_end_time' => '8:8:8',
            'pause_start_time' => '7:7:7',
            'pause_end_time' => '6:6:6',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#FC73AD'
        ]);
        $users[] = User::create([
            'name' => 'Ruslan2',
            'email' => 'menrusamen1999@gmail.com',
            'password' => Hash::make('password'),
            'work_start_time' => '9:9:9',
            'work_end_time' => '8:8:8',
            'pause_start_time' => '7:7:7',
            'pause_end_time' => '6:6:6',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#FC73AD'
        ]);
        $roles = config('params.roles');
        foreach ($users as $user) {
            $user->syncRoles($roles[rand(0, 6)]);
        }
    }
}
