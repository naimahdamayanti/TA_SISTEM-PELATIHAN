<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KualifikasiSertifikasiModel extends Model
{
    protected $table      = 'kualifikasi_sertifikasi';
    protected $primaryKey = 'id_kualifikasi';

    // Hanya ada tgl_penilaian sebagai created, tidak ada updated_at
    public $timestamps = false;

    protected $fillable = [
        'pendaftaran_id',
        'instruktur_id',
        'persen_hadir',
        'memenuhi_syarat',
        'catatan',
        'tgl_penilaian',
    ];

    protected function casts(): array
    {
        return [
            'persen_hadir'    => 'float',
            'memenuhi_syarat' => 'boolean',
            'tgl_penilaian'   => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // RELATIONS
    // ----------------------------------------------------------------

    /** Pendaftaran yang dinilai */
    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranModel::class, 'pendaftaran_id', 'id_pendaftaran');
    }

    /** Instruktur yang memberikan penilaian */
    public function instruktur()
    {
        return $this->belongsTo(UserModel::class, 'instruktur_id', 'id_user');
    }

    // ----------------------------------------------------------------
    // HELPERS
    // ----------------------------------------------------------------

    /** Batas minimal kehadiran untuk memenuhi syarat sertifikasi */
    const BATAS_KEHADIRAN = 80.0;

    public function layakSertifikat(): bool
    {
        return $this->memenuhi_syarat && $this->persen_hadir >= self::BATAS_KEHADIRAN;
    }
}
