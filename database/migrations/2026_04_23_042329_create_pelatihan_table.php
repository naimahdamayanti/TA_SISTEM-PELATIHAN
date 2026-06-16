<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihan', function (Blueprint $table) {
            $table->id('id_pelatihan');
            $table->foreignId('instruktur_id')
                  ->constrained('users', 'id_user')
                  ->cascadeOnDelete();
            $table->string('nama_pelatihan', 100);
            $table->string('kode_pelatihan', 15)->unique();
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->foreign('kategori')
                  ->references('id_kategori')
                  ->on('kategori')
                  ->onUpdate('cascade');
            $table->text('deskripsi');
            $table->integer('kuota')->comment('Maksimal jumlah peserta');
            $table->date('tgl_mulai')->nullable()->comment('Tanggal sesi pertama');
            $table->date('tgl_selesai')->nullable()->comment('Tanggal sesi terakhir');
            $table->enum('status', ['tersedia', 'sedang berlangsung', 'selesai'])->default('tersedia');
            $table->string('template_sertifikat')->nullable();
            $table->json('posisi_sertifikat')->nullable();
            $table->string('tanda_tangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihan');
    }
};
