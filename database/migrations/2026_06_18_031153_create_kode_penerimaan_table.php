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
        Schema::create('kode_penerimaan', function (Blueprint $table) {
        $table->id('id_kode_penerimaan');
        $table->string('kode', 20)->unique();
        $table->string('nama_peruntukan')->nullable();
        $table->boolean('is_used')->default(false);
        $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('used_at')->nullable();
        $table->date('expired_at')->nullable();
        $table->foreignId('created_by')->constrained('users');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_penerimaan');
    }
};
