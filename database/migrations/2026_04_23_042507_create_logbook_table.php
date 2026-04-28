<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logbook', function (Blueprint $table) {
            $table->id('id_logbook');

            $table->foreignId('instruktur_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('sesi_id')->constrained('sesi_pelatihan', 'id_sesi')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('users', 'id_user')->cascadeOnDelete();

            $table->enum('status', ['hadir','izin','tidak hadir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook');
    }
};
