<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kualifikasi_sertifikasi', function (Blueprint $table) {
            $table->id('id_kualifikasi');

            // Satu pendaftaran hanya punya satu penilaian kualifikasi
            $table->foreignId('pendaftaran_id')
                  ->unique()
                  ->constrained('pendaftaran', 'id_pendaftaran')
                  ->cascadeOnDelete();

            $table->foreignId('instruktur_id')
                  ->constrained('users', 'id_user')
                  ->cascadeOnDelete();

            // Persentase kehadiran dihitung otomatis dari tabel logbook
            $table->decimal('persen_hadir', 5, 2)->comment('Persentase kehadiran (0.00 - 100.00)');

            // true  = memenuhi syarat (persen_hadir >= 80%)
            // false = tidak memenuhi syarat
            $table->boolean('memenuhi_syarat')->default(false);

            $table->text('catatan')->nullable()->comment('Catatan penilaian dari instruktur');
            $table->dateTime('tgl_penilaian')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kualifikasi_sertifikasi');
    }
};
