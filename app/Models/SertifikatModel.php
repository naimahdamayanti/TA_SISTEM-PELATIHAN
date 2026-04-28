<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatModel extends Model
{
    protected $table = 'sertifikat';
    protected $primaryKey = 'id_sertifikat';
    public $timestamps = false;

    protected $fillable = [
        'pendaftaran_id',
        'kode_sertifikat',
        'tgl_terbit',
        'diterbitkan_oleh',
        'file'
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranModel::class, 'pendaftaran_id');
    }
}
