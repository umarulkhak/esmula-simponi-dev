<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $with = [
        'user',
        'siswa',
        'tagihanDetails',
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
    public function tagihanDetails(): HasMany
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

        // ✅ Perbaikan utama: gunakan relasi tagihanDetails()
        static::deleting(function (self $tagihan) {
            $tagihan->tagihanDetails()->delete();
        });
    }

    // ✅ Scope untuk pencarian (nama atau NISN siswa)
    public function scopeSearch($query, $term)
    {
        return $query->whereHas('siswa', function ($q) use ($term) {
            $q->where('nama', 'like', "%{$term}%")
              ->orWhere('nisn', 'like', "%{$term}%");
        });
    }
}
