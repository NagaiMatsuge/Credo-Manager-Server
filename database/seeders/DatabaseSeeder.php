<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            StepSeeder::class,
            TaskSeeder::class,
            TaskUserSeeder::class,
            MessageSeeder::class,
            PaymentSeeder::class,
            NoteSeeder::class,
            NotificationSeeder::class,
            NotificationUserSeeder::class,
            ServerSeeder::class,
            FtpSeeder::class,
            DbAccessSeeder::class
        ]);
    }
}
