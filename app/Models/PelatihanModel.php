<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelatihanModel extends Model
{
    protected $table = 'pelatihan';
    protected $primaryKey = 'id_pelatihan';
    public $timestamps = false;

    protected $fillable = [
        'instruktur_id',
        'nama_pelatihan',
        'kode_pelatihan',
        'kategori',
        'deskripsi',
        'kuota',
        'status'
    ];

    public function instruktur()
    {
        return $this->belongsTo(User::class, 'instruktur_id');
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranModel::class, 'pelatihan_id');
    }

    public function sesi()
    {
        return $this->hasMany(SesiPelatihanModel::class, 'pelatihan_id');
    }
}
