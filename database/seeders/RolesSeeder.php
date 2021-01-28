<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('params.roles');
        $res = [];
        foreach ($roles as $role) {
            $res[] = [
                'name' => $role
            ];
        }
        DB::table('roles')->insert($res);
    }
}
