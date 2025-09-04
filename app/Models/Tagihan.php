<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tagihan extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass-assignment.
     *
     * @var array<int, string>
     */
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
     * Tipe data tanggal.
     *
     * @var array<int, string>
     */
    protected $dates = [
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
    ];

    /* ==========================
     |  RELASI
     |==========================*/

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

    /* ==========================
     |  CUSTOM ATTRIBUTE / HELPER
     |==========================*/

    /**
     * Format field tertentu ke dalam format Rupiah.
     *
     * @param  string  $field
     * @return string
     */
    public function formatRupiah(string $field = 'jumlah_biaya'): string
    {
        $value = $this->$field ?? 0;
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    /* ==========================
     |  EVENT MODEL
     |==========================*/

    /**
     * Event model untuk otomatis mengisi user_id
     * saat create dan update.
     */
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
