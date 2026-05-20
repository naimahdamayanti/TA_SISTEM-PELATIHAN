<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookModel extends Model
{
    protected $table      = 'logbook';
    protected $primaryKey = 'id_logbook';

    protected $fillable = [
        'instruktur_id',
        'sesi_id',
        'peserta_id',
        'status',
        'catatan',
    ];

    // ----------------------------------------------------------------
    // RELATIONS
    // ----------------------------------------------------------------

    /** Instruktur yang mencatat absensi ini */
    public function instruktur()
    {
        return $this->belongsTo(UserModel::class, 'instruktur_id', 'id_user');
    }

    /** Sesi pelatihan tempat absensi ini dicatat */
    public function sesiPelatihan()
    {
        return $this->belongsTo(SesiPelatihanModel::class, 'sesi_id', 'id_sesi');
    }

    /** Peserta yang diabsen */
    public function peserta()
    {
        return $this->belongsTo(UserModel::class, 'peserta_id', 'id_user');
    }

    // ----------------------------------------------------------------
    // HELPERS
    // ----------------------------------------------------------------

    public function hadir(): bool
    {
        return $this->status === 'hadir';
    }

    /**
     * Hitung persentase kehadiran peserta pada satu pelatihan.
     * Digunakan sebagai input kualifikasi sertifikasi.
     */
    public static function persentaseKehadiran(int $pesertaId, int $pelatihanId): float
    {
        // Total sesi pada pelatihan
        $totalSesi = SesiPelatihanModel::where('pelatihan_id', $pelatihanId)->count();

        if ($totalSesi === 0) {
            return 0.0;
        }

        // Jumlah sesi yang dihadiri peserta
        $totalHadir = self::whereHas('sesiPelatihan', function ($q) use ($pelatihanId) {
                            $q->where('pelatihan_id', $pelatihanId);
                        })
                        ->where('peserta_id', $pesertaId)
                        ->where('status', 'hadir')
                        ->count();

        return round(($totalHadir / $totalSesi) * 100, 2);
    }
}
