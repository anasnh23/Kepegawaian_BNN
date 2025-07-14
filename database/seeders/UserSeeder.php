<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Truncate the table to remove existing data (avoids duplicates)

        // Now insert the data
        DB::table('m_user')->insert([
            [
                'id_level' => 1,
                'nip' => '198101011998031001',
                'email' => 'admin@bnn.go.id',
                'nama' => 'Ahmad Admin',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'no_tlp' => '081234567890',
                'foto' => 'admin.jpg'
            ],
            [
                'id_level' => 2,
                'nip' => '199002021999041002',
                'email' => 'pegawai@bnn.go.id',
                'nama' => 'Budi Pegawai',
                'username' => 'budi',
                'password' => Hash::make('budi123'),
                'jenis_kelamin' => 'L',
                'agama' => 'Kristen',
                'no_tlp' => '089876543210',
                'foto' => 'budi.jpg'
            ],
        ]);
    }
}