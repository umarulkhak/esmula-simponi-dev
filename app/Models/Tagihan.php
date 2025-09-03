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
        'nama_biaya',
        'jumlah_biaya',
        'keterangan',
        'status',
    ];

    /**
     * Relasi ke User (pembuat tagihan).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Format angka ke dalam bentuk Rupiah.
     */
    public function formatRupiah($field = 'jumlah_biaya'): string
    {
        $value = $this->$field ?? 0;
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    /**
     * Event model untuk otomatis mengisi user_id.
     */
    protected static function booted(): void
    {
        static::creating(fn($tagihan) => $tagihan->user_id = auth()->id());
        static::updating(fn($tagihan) => $tagihan->user_id = auth()->id());
    }
}
