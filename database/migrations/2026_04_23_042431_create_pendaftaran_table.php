<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id('id_pendaftaran');

            // peserta_id nullable karena bisa daftar tanpa akun (guest)
            $table->foreignId('peserta_id')
                  ->nullable()
                  ->constrained('users', 'id_user')
                  ->nullOnDelete();

            $table->foreignId('pelatihan_id')
                  ->constrained('pelatihan', 'id_pelatihan')
                  ->cascadeOnDelete();

            // Data formulir pendaftaran
            $table->string('email', 100);
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('perusahaan', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('tlp_perusahaan', 20)->nullable();
            $table->text('pesan')->nullable();

            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->dateTime('tgl_daftar')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
