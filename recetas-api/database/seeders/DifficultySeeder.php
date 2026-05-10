<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DifficultySeeder extends Seeder
{
    public function run(): void
    {
        $difficulties = [
            ['name' => 'Fácil',  'slug' => 'facil'],
            ['name' => 'Media',  'slug' => 'media'],
            ['name' => 'Difícil','slug' => 'dificil'],
        ];

        DB::table('difficulties')->insertOrIgnore($difficulties);
    }
}
