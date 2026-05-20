<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('no_hp', 20)->nullable();
            $table->string('foto_profil')->nullable()->comment('Path file foto profil');
            $table->enum('role', ['admin', 'instruktur', 'peserta']);
            $table->string('api_token', 80)->nullable()->unique();
            $table->string('token_reset', 100)->nullable()->comment('Token untuk reset password');
            $table->dateTime('token_exp')->nullable()->comment('Waktu kedaluwarsa token reset');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
