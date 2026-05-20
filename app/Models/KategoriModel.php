<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class KategoriModel extends Model
{
    protected $table      = 'kategori';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
        'aktif',
    ];

    // Scope untuk hanya mengambil kategori yang aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    // ── Relasi ──────────────────────────────────────────────────────────
    public function pelatihan()
    {
        return $this->hasMany(PelatihanModel::class, 'kategori_id', 'id_kategori');
    }
}