<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\KodePenerimaanModel;
class UserModel extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table      = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'username',
        'password',
        'no_hp',
        'foto_profil',
        'role',
        'kode_penerimaan_id',
        'bukti_penerimaan',
        'status_verifikasi',
        'catatan_verifikasi',
        'api_token',
        'token_reset',
        'token_exp',
    ];

    protected $hidden = [
        'password',
        'api_token',
        'token_reset',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'token_exp' => 'datetime',
        ];
    }

    // ----------------------------------------------------------------
    // RELATIONS
    // ----------------------------------------------------------------

    /** Pelatihan yang diampu oleh instruktur ini */
    public function pelatihan()
    {
        return $this->hasMany(PelatihanModel::class, 'instruktur_id', 'id_user');
    }

    /** Pendaftaran pelatihan milik peserta ini */
    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranModel::class, 'peserta_id', 'id_user');
    }

    /** Logbook absensi sebagai peserta */
    public function logbookSebagaiPeserta()
    {
        return $this->hasMany(LogbookModel::class, 'peserta_id', 'id_user');
    }

    /** Logbook absensi yang dicatat sebagai instruktur */
    public function logbookSebagaiInstruktur()
    {
        return $this->hasMany(LogbookModel::class, 'instruktur_id', 'id_user');
    }

    /** Penilaian kualifikasi yang diberikan sebagai instruktur */
    public function kualifikasiSertifikasi()
    {
        return $this->hasMany(KualifikasiSertifikasiModel::class, 'instruktur_id', 'id_user');
    }

    // ----------------------------------------------------------------
    // HELPERS
    // ----------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstruktur(): bool
    {
        return $this->role === 'instruktur';
    }

    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    public function kodePenerimaan(): BelongsTo
    {
        return $this->belongsTo(KodePenerimaanModel::class, 'kode_penerimaan_id');
    }

    public function isVerified(): bool
    {
        return $this->status_verifikasi === 'terverifikasi';
    }
}
