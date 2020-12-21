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
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'work_start_time' => '9:00',
            'work_end_time' => '18:00',
            'pause_start_time' => '13:00',
            'pause_end_time' => '14:00',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#FC73AD'
        ]);
        $users[] = User::create([
            'name' => 'Bobur',
            'email' => 'komilovboburweb@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'work_start_time' => '9:00',
            'work_end_time' => '18:00',
            'pause_start_time' => '13:00',
            'pause_end_time' => '14:00',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#FCB573'
        ]);
        $users[] = User::create([
            'name' => 'Shohrux',
            'email' => 'shohrukh.mamirov@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'work_start_time' => '9:00',
            'work_end_time' => '18:00',
            'pause_start_time' => '13:00',
            'pause_end_time' => '14:00',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#FC73AD'
        ]);
        $users[] = User::create([
            'name' => 'Ruslan2',
            'email' => 'menrusamen1999@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'work_start_time' => '9:00',
            'work_end_time' => '18:00',
            'pause_start_time' => '13:00',
            'pause_end_time' => '14:00',
            'working_days' => [1, 2, 3, 4, 5],
            'color' => '#8F73FC'
        ]);
        $roles = config('params.roles');
        foreach ($users as $user) {
            $user->syncRoles($roles[rand(0, 6)]);
        }
    }
}
