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
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id('id_sertifikat');

            $table->foreignId('pendaftaran_id')->constrained('pendaftaran', 'id_pendaftaran')->cascadeOnDelete();

            $table->string('kode_sertifikat', 10);
            $table->dateTime('tgl_terbit');
            $table->string('diterbitkan_oleh', 50);
            $table->string('file', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
