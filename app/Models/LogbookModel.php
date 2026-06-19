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

    public function instruktur()
    {
        return $this->belongsTo(UserModel::class, 'instruktur_id', 'id_user');
    }

    public function sesiPelatihan()
    {
        return $this->belongsTo(SesiPelatihanModel::class, 'sesi_id', 'id_sesi');
    }

    public function peserta()
    {
        return $this->belongsTo(UserModel::class, 'peserta_id', 'id_user');
    }

    public function hadir(): bool
    {
        return $this->status === 'hadir';
    }

    public static function persentaseKehadiran(int $pesertaId, int $pelatihanId): float
    {
        $totalSesi = SesiPelatihanModel::where('pelatihan_id', $pelatihanId)->count();

        if ($totalSesi === 0) {
            return 0.0;
        }

        $totalHadir = self::whereHas('sesiPelatihan', function ($q) use ($pelatihanId) {
                            $q->where('pelatihan_id', $pelatihanId);
                        })
                        ->where('peserta_id', $pesertaId)
                        ->where('status', 'hadir')
                        ->count();

        return round(($totalHadir / $totalSesi) * 100, 2);
    }
}
