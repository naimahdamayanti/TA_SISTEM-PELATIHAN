<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook', function (Blueprint $table) {
            $table->id('id_logbook');

            $table->foreignId('instruktur_id')
                  ->constrained('users', 'id_user')
                  ->cascadeOnDelete();

            $table->foreignId('sesi_id')
                  ->constrained('sesi_pelatihan', 'id_sesi')
                  ->cascadeOnDelete();

            $table->foreignId('peserta_id')
                  ->constrained('users', 'id_user')
                  ->cascadeOnDelete();

            $table->enum('status', ['hadir', 'izin', 'tidak hadir']);
            $table->string('catatan', 255)->nullable()->comment('Catatan tambahan dari instruktur');
            $table->timestamps();

            // Satu peserta hanya boleh punya satu entri absensi per sesi
            $table->unique(['sesi_id', 'peserta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook');
    }
};
