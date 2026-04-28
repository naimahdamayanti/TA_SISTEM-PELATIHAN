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
        Schema::create('pelatihan', function (Blueprint $table) {
            $table->id('id_pelatihan');
            $table->foreignId('instruktur_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('nama_pelatihan', 50);
            $table->string('kode_pelatihan', 10);
            $table->string('kategori', 20);
            $table->text('deskripsi');
            $table->string('kuota', 10);
            $table->enum('status', ['tersedia','penuh']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihan');
    }
};
