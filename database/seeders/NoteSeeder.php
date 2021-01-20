<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $words = ['some', 'random', 'text', 'hello', 'school', 'diamond', 'kalaka', 'malaka', 'jibrish', 'united', 'manchester', 'brazil', 'tormorrow', 'mother', 'lalala', 'said', 'yesterday', 'or', 'went', 'will', 'break', 'scoop'];
        $user_ids = User::get()->pluck('id');
        $notes = [];
        foreach ($user_ids as $user_id) {
            $max = rand(0, count($words) - 1);
            $word = "";
            for ($i = 0; $i < $max; $i++) {
                $word .= " " . $words[rand(0, count($words) - 1)];
            }
            $notes[] = [
                'text' => $word,
                'user_id' => $user_id,
                'created_at' => now()
            ];
        }
        DB::table("notes")->insert($notes);
    }
}
