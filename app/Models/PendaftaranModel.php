<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranModel extends Model
{
    protected $table = 'pendaftaran';
    protected $primaryKey = 'id_pendaftaran';
    public $timestamps = false;

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
        'tgl_daftar'
    ];

    public function peserta()
    {
        return $this->belongsTo(User::class, 'peserta_id');
    }

    public function pelatihan()
    {
        return $this->belongsTo(PelatihanModel::class, 'pelatihan_id');
    }

    public function sertifikat()
    {
        return $this->hasOne(SertifikatModel::class, 'pendaftaran_id');
    }
}
