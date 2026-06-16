<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id('id_sertifikat');

            // Satu pendaftaran hanya bisa punya satu sertifikat
            $table->foreignId('pendaftaran_id')
                  ->unique()
                  ->constrained('pendaftaran', 'id_pendaftaran')
                  ->cascadeOnDelete();

            $table->string('kode_sertifikat', 20)->unique();
            $table->string('nomor_sertifikat', 60)->nullable();
            $table->dateTime('tgl_terbit');
            $table->string('diterbitkan_oleh', 100);
            $table->string('file', 255)->comment('Path file PDF sertifikat');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
