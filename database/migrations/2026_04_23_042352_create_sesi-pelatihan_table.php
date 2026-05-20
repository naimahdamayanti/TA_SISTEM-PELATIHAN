<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_pelatihan', function (Blueprint $table) {
            $table->id('id_sesi');
            $table->foreignId('pelatihan_id')
                  ->constrained('pelatihan', 'id_pelatihan')
                  ->cascadeOnDelete();
            $table->string('judul_sesi', 100)->nullable()->comment('Judul / materi sesi');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('lokasi', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_pelatihan');
    }
};
