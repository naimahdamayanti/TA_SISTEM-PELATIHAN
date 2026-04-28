<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'email',
        'username',
        'password',
        'role'
    ];

    // Relasi
    public function pelatihan()
    {
        return $this->hasMany(PelatihanModel::class, 'instruktur_id');
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranModel::class, 'peserta_id');
    }
}
