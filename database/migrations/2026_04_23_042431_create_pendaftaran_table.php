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
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id('id_pendaftaran');

            $table->foreignId('peserta_id')->nullable()->constrained('users', 'id_user');
            $table->foreignId('pelatihan_id')->nullable()->constrained('pelatihan', 'id_pelatihan');

            $table->string('email', 50)->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('perusahaan', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('tlp_perusahaan', 20)->nullable();
            $table->text('pesan')->nullable();

            $table->enum('status', ['menunggu','diterima','ditolak'])->default('menunggu');
            $table->dateTime('tgl_daftar')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
