<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PelatihanSeeder extends Seeder
{
    public function run(): void
    {
        // instruktur_id 2 = Naimah, instruktur_id 3 = Budi
        DB::table('pelatihan')->insert([
            [
                'instruktur_id'  => 2,
                'nama_pelatihan' => 'K3 Umum Dasar',
                'kode_pelatihan' => 'K3-001',
                'kategori'       => 'Keselamatan Kerja',
                'deskripsi'      => 'Pelatihan Keselamatan dan Kesehatan Kerja tingkat dasar untuk semua karyawan. Mencakup identifikasi bahaya, penggunaan APD, dan prosedur darurat.',
                'kuota'          => 30,
                'tgl_mulai'      => '2026-05-05',
                'tgl_selesai'    => '2026-05-07',
                'status'         => 'tersedia',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'instruktur_id'  => 2,
                'nama_pelatihan' => 'Operator Forklift',
                'kode_pelatihan' => 'OPR-001',
                'kategori'       => 'Operasional',
                'deskripsi'      => 'Pelatihan dan sertifikasi operator forklift sesuai standar Kemnaker RI. Mencakup teori dasar, praktik lapangan, dan ujian kompetensi.',
                'kuota'          => 15,
                'tgl_mulai'      => '2026-05-12',
                'tgl_selesai'    => '2026-05-14',
                'status'         => 'tersedia',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'instruktur_id'  => 3,
                'nama_pelatihan' => 'First Aid & P3K',
                'kode_pelatihan' => 'P3K-001',
                'kategori'       => 'Kesehatan',
                'deskripsi'      => 'Pelatihan pertolongan pertama pada kecelakaan kerja. Mencakup penanganan luka, RJP, dan penggunaan AED.',
                'kuota'          => 20,
                'tgl_mulai'      => '2026-04-01',
                'tgl_selesai'    => '2026-04-03',
                'status'         => 'selesai',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'instruktur_id'  => 3,
                'nama_pelatihan' => 'Scaffolding Dasar',
                'kode_pelatihan' => 'SCF-001',
                'kategori'       => 'Konstruksi',
                'deskripsi'      => 'Pelatihan pemasangan dan pembongkaran perancah (scaffolding) sesuai standar SNI.',
                'kuota'          => 10,
                'tgl_mulai'      => '2026-06-02',
                'tgl_selesai'    => '2026-06-04',
                'status'         => 'tersedia',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}