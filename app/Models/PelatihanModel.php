<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelatihanModel extends Model
{
    protected $table      = 'pelatihan';
    protected $primaryKey = 'id_pelatihan';

    protected $fillable = [
        'instruktur_id',
        'nama_pelatihan',
        'kode_pelatihan',
        'kategori_id',
        'deskripsi',
        'kuota',
        'tgl_mulai',
        'tgl_selesai',
        'status',
        'template_sertifikat',
        'posisi_sertifikat',
        'tanda_tangan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai'   => 'date',
            'tgl_selesai' => 'date',
            'kuota'       => 'integer',
            'posisi_sertifikat' => 'array',
        ];
    }

    public function instruktur()
    {
        return $this->belongsTo(UserModel::class, 'instruktur_id', 'id_user');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'id_kategori');
    }

    public function sesiPelatihan()
    {
        return $this->hasMany(SesiPelatihanModel::class, 'pelatihan_id', 'id_pelatihan');
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranModel::class, 'pelatihan_id', 'id_pelatihan');
    }

    public function pendaftaranDiterima()
    {
        return $this->hasMany(PendaftaranModel::class, 'pelatihan_id', 'id_pelatihan')
                    ->where('status', 'diterima');
    }

    public function jumlahPeserta(): int
    {
        return $this->pendaftaranDiterima()->count();
    }

    public function masihTersedia(): bool
    {
        return $this->status === 'tersedia' && $this->jumlahPeserta() < $this->kuota;
    }
}
