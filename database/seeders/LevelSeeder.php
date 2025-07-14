<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_level')->insert([
            ['level_name' => 'Admin'],
            ['level_name' => 'Pegawai'],
        ]);
    }
}

