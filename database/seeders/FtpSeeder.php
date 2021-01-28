<?php

namespace Database\Seeders;

use App\Models\Server;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FtpSeeder extends Seeder
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
        $ftpAccesses = [];
        $ftpAccesses[] = [
            'server_id' => 1,
            'port' => 21,
            'host' => 'Some random host',
            'login' => 'Login of the ftp user',
            'password' => 'password',
            'description' => 'This was supposed to be some random description for some random ftp access'
        ];
        $ftpAccesses[] = [
            'server_id' => 2,
            'port' => 21,
            'host' => 'Random host',
            'login' => 'Here we have login',
            'password' => 'passwordssssss',
            'description' => 'And now this is another random description for the another ftp access'
        ];
        $ftpAccesses[] = [
            'server_id' => 2,
            'port' => 21,
            'host' => 'Random host',
            'login' => 'Here we have login',
            'password' => 'passwordssssss',
            'description' => 'And now this is another random description for the another ftp access'
        ];
        $ftpAccesses[] = [
            'server_id' => 2,
            'port' => 21,
            'host' => 'Random host',
            'login' => 'Here we have login',
            'password' => 'passwordssssss',
            'description' => 'And now this is another random description for the another ftp access'
        ];
        $ftpAccesses[] = [
            'server_id' => 2,
            'port' => 21,
            'host' => 'Random host',
            'login' => 'Here we have login',
            'password' => 'passwordssssss',
            'description' => 'And now this is another random description for the another ftp access'
        ];

        DB::table('ftp_access')->insert($ftpAccesses);
    }
}
