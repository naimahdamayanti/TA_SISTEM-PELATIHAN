<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // ADMIN
            [
                'nama'       => 'Administrator',
                'email'      => 'admin@gmail.com',
                'username'   => 'admin',
                'password'   => Hash::make('admin123'),
                'no_hp'      => '081200000001',
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // INSTRUKTUR
            [
                'nama'       => 'Naimah Rahayu',
                'email'      => 'naimah@gmail.com',
                'username'   => 'naimah',
                'password'   => Hash::make('instruktur123'),
                'no_hp'      => '081200000002',
                'role'       => 'instruktur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Budi Hartono',
                'email'      => 'budi@gmail.com',
                'username'   => 'budi',
                'password'   => Hash::make('instruktur123'),
                'no_hp'      => '081200000003',
                'role'       => 'instruktur',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // PESERTA
            [
                'nama'       => 'Alissya Putri',
                'email'      => 'alissya@gmail.com',
                'username'   => 'alissya',
                'password'   => Hash::make('peserta123'),
                'no_hp'      => '081200000004',
                'role'       => 'peserta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Dendi Saputra',
                'email'      => 'dendi@gmail.com',
                'username'   => 'dendi',
                'password'   => Hash::make('peserta123'),
                'no_hp'      => '081200000005',
                'role'       => 'peserta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Rina Wulandari',
                'email'      => 'rina@gmail.com',
                'username'   => 'rina',
                'password'   => Hash::make('peserta123'),
                'no_hp'      => '081200000006',
                'role'       => 'peserta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}