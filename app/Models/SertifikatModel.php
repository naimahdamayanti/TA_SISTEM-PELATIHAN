<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatModel extends Model
{
    protected $table      = 'sertifikat';
    protected $primaryKey = 'id_sertifikat';

    const UPDATED_AT = null;

    protected $fillable = [
        'pendaftaran_id',
        'kode_sertifikat',
        'nomor_sertifikat',
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

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranModel::class, 'pendaftaran_id', 'id_pendaftaran');
    }

    public function peserta()
    {
        return $this->hasOneThrough(
            UserModel::class,
            PendaftaranModel::class,
            'id_pendaftaran', 
            'id_user',        
            'pendaftaran_id', 
            'peserta_id'      
        );
    }

    public function urlFile(): string
    {
        return asset('storage/' . $this->file);
    }
}
