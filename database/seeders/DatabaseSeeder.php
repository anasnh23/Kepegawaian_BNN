<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder untuk tabel m_level dan m_user
        $this->call([
            LevelSeeder::class,
            UserSeeder::class,
        ]);
    }
}