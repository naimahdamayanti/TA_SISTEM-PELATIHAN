<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KualifikasiSertifikasiModel extends Model
{
    protected $table      = 'kualifikasi_sertifikasi';
    protected $primaryKey = 'id_kualifikasi';
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
            'tgl_penilaian'   => 'datetime',
        ];
    }

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranModel::class, 'pendaftaran_id', 'id_pendaftaran');
    }

    public function instruktur()
    {
        return $this->belongsTo(UserModel::class, 'instruktur_id', 'id_user');
    }

    const BATAS_KEHADIRAN = 80.0;

    public function layakSertifikat(): bool
    {
        return $this->memenuhi_syarat === 'lulus' && $this->persen_hadir >= self::BATAS_KEHADIRAN;
    }
}
