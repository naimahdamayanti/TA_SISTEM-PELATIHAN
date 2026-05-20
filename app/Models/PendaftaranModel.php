<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranModel extends Model
{
    protected $table      = 'pendaftaran';
    protected $primaryKey = 'id_pendaftaran';

    // Timestamps non-standar: tgl_daftar (created) + updated_at
    const CREATED_AT = 'tgl_daftar';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'peserta_id',
        'pelatihan_id',
        'email',
        'first_name',
        'last_name',
        'perusahaan',
        'alamat',
        'no_hp',
        'pekerjaan',
        'tlp_perusahaan',
        'pesan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_daftar' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // RELATIONS
    // ----------------------------------------------------------------

    /** Peserta (user) yang mendaftar — nullable jika guest */
    public function peserta()
    {
        return $this->belongsTo(UserModel::class, 'peserta_id', 'id_user');
    }

    /** Pelatihan yang didaftarkan */
    public function pelatihan()
    {
        return $this->belongsTo(PelatihanModel::class, 'pelatihan_id', 'id_pelatihan');
    }

    /** Penilaian kualifikasi sertifikasi pendaftaran ini */
    public function kualifikasiSertifikasi()
    {
        return $this->hasOne(KualifikasiSertifikasiModel::class, 'pendaftaran_id', 'id_pendaftaran');
    }

    /** Sertifikat yang diterbitkan untuk pendaftaran ini */
    public function sertifikat()
    {
        return $this->hasOne(SertifikatModel::class, 'pendaftaran_id', 'id_pendaftaran');
    }

    // ----------------------------------------------------------------
    // HELPERS
    // ----------------------------------------------------------------

    public function namaLengkap(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function sudahDiterima(): bool
    {
        return $this->status === 'diterima';
    }

    public function sudahBersertifikat(): bool
    {
        return $this->sertifikat()->exists();
    }
}
