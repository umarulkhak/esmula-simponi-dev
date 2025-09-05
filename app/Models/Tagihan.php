<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'siswa_id',
        'angkatan',
        'kelas',
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
        'keterangan',
        'status',
    ];

    protected $dates = [
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Siswa
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke TagihanDetail
    public function details()
    {
        return $this->hasMany(TagihanDetail::class);
    }

    public function formatRupiah(string $field = 'jumlah_biaya'): string
    {
        $value = $this->$field ?? 0;
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    protected static function booted(): void
    {
        static::creating(function (self $tagihan) {
            $tagihan->user_id = auth()->id();
        });

        static::updating(function (self $tagihan) {
            $tagihan->user_id = auth()->id();
        });
    }
}
