<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelatihanModel extends Model
{
    protected $table      = 'pelatihan';
    protected $primaryKey = 'id_pelatihan';

    protected $fillable = [
        'instruktur_id',
        'nama_pelatihan',
        'kode_pelatihan',
        'kategori',
        'deskripsi',
        'kuota',
        'tgl_mulai',
        'tgl_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai'   => 'date',
            'tgl_selesai' => 'date',
            'kuota'       => 'integer',
        ];
    }

    // ----------------------------------------------------------------
    // RELATIONS
    // ----------------------------------------------------------------

    /** Instruktur yang mengampu pelatihan ini */
    public function instruktur()
    {
        return $this->belongsTo(UserModel::class, 'instruktur_id', 'id_user');
    }

    /** Semua sesi jadwal pelatihan ini */
    public function sesiPelatihan()
    {
        return $this->hasMany(SesiPelatihanModel::class, 'pelatihan_id', 'id_pelatihan');
    }

    /** Semua pendaftaran ke pelatihan ini */
    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranModel::class, 'pelatihan_id', 'id_pelatihan');
    }

    /** Pendaftaran yang sudah diterima */
    public function pendaftaranDiterima()
    {
        return $this->hasMany(PendaftaranModel::class, 'pelatihan_id', 'id_pelatihan')
                    ->where('status', 'diterima');
    }

    // ----------------------------------------------------------------
    // HELPERS
    // ----------------------------------------------------------------

    /** Jumlah peserta yang sudah diterima */
    public function jumlahPeserta(): int
    {
        return $this->pendaftaranDiterima()->count();
    }

    /** Apakah kuota masih tersisa */
    public function masihTersedia(): bool
    {
        return $this->status === 'tersedia' && $this->jumlahPeserta() < $this->kuota;
    }
}
