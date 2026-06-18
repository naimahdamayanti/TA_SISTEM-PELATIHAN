<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
class KodePenerimaanModel extends Model
{
    protected $table = 'kode_penerimaan';
    protected $primaryKey = 'id_kode_penerimaan';

    protected $fillable = [
        'kode', 'nama_peruntukan', 'is_used',
        'used_by', 'used_at', 'expired_at', 'created_by',
    ];

    protected $casts = [
        'is_used'    => 'boolean',
        'used_at'    => 'datetime',
        'expired_at' => 'date',
    ];

    public function pemakainya(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'used_by');
    }

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    public function isAvailable(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    // Generate kode unik format: EXP-INSTR-XXXXXX
    public static function generateKode(): string
    {
        do {
            $kode = 'EXP-INSTR-' . strtoupper(Str::random(6));
        } while (self::where('kode', $kode)->exists());

        return $kode;
    }
}