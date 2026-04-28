<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookModel extends Model
{
    protected $table = 'logbook';
    protected $primaryKey = 'id_logbook';
    public $timestamps = false;

    protected $fillable = [
        'instruktur_id',
        'sesi_id',
        'peserta_id',
        'status'
    ];

    public function instruktur()
    {
        return $this->belongsTo(UserModel::class, 'instruktur_id');
    }

    public function peserta()
    {
        return $this->belongsTo(UserModel::class, 'peserta_id');
    }

    public function sesi()
    {
        return $this->belongsTo(SesiPelatihanModel::class, 'sesi_id');
    }
}
