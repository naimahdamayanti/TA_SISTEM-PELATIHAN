<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatModel extends Model
{
    protected $table      = 'sertifikat';
    protected $primaryKey = 'id_sertifikat';

    // Hanya ada created_at, tidak ada updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'pendaftaran_id',
        'kode_sertifikat',
        'tgl_terbit',
        'diterbitkan_oleh',
        'file',
    ];

    protected function casts(): array
    {
        return [
            'tgl_terbit' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // RELATIONS
    // ----------------------------------------------------------------

    /** Pendaftaran yang memiliki sertifikat ini */
    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranModel::class, 'pendaftaran_id', 'id_pendaftaran');
    }

    /** Shortcut ke peserta melalui pendaftaran */
    public function peserta()
    {
        return $this->hasOneThrough(
            UserModel::class,
            PendaftaranModel::class,
            'id_pendaftaran', // FK di pendaftaran
            'id_user',        // FK di users
            'pendaftaran_id', // local key di sertifikat
            'peserta_id'      // local key di pendaftaran
        );
    }

    // ----------------------------------------------------------------
    // HELPERS
    // ----------------------------------------------------------------

    /** URL / path lengkap file PDF sertifikat */
    public function urlFile(): string
    {
        return asset('storage/' . $this->file);
    }
}
