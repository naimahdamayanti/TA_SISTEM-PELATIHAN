<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPelatihanModel extends Model
{
    protected $table      = 'sesi_pelatihan';
    protected $primaryKey = 'id_sesi';

    protected $fillable = [
        'pelatihan_id',
        'judul_sesi',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    // ----------------------------------------------------------------
    // RELATIONS
    // ----------------------------------------------------------------

    /** Pelatihan induk sesi ini */
    public function pelatihan()
    {
        return $this->belongsTo(PelatihanModel::class, 'pelatihan_id', 'id_pelatihan');
    }

    /** Semua data logbook / absensi pada sesi ini */
    public function logbook()
    {
        return $this->hasMany(LogbookModel::class, 'sesi_id', 'id_sesi');
    }
}
