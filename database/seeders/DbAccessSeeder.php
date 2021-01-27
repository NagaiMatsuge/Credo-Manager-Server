<?php

namespace Database\Seeders;

use App\Models\Server;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DbAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $serverIds = Server::get()->pluck('id');
        $serverCount = count($serverIds);
        $dbAccesses = [];
        $dbAccesses[] = [
            'db_name' => 'artisans',
            'host' => 'artisan command',
            'login' => 'artisan',
            'password' => 'artisan',
            'description' => 'Long long long description, (actually no)',
            'server_id' => 1,
        ];
        $dbAccesses[] = [
            'db_name' => 'White hat',
            'host' => 'whitehat.com',
            'login' => 'hatshatshats',
            'password' => 'guessmypassword',
            'description' => 'Totally random text',
            'server_id' => 2,
        ];
        $dbAccesses[] = [
            'db_name' => 'White hat',
            'host' => 'whitehat.com',
            'login' => 'hatshatshats',
            'password' => 'guessmypassword',
            'description' => 'Totally random text',
            'server_id' => 2,
        ];
        $dbAccesses[] = [
            'db_name' => 'White hat',
            'host' => 'whitehat.com',
            'login' => 'hatshatshats',
            'password' => 'guessmypassword',
            'description' => 'Totally random text',
            'server_id' => 2,
        ];

        DB::table("db_access")->insert($dbAccesses);
    }
}
