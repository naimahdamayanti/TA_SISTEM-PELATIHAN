<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPelatihanModel extends Model
{
    protected $table = 'sesi_pelatihan';
    protected $primaryKey = 'id_sesi';
    public $timestamps = false;

    protected $fillable = [
        'pelatihan_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi'
    ];

    public function pelatihan()
    {
        return $this->belongsTo(PelatihanModel::class, 'pelatihan_id');
    }
}
