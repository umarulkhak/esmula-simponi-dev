<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tagihan;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tagihan_id',
        'wali_id',
        'tanggal_bayar',
        'status_konfirmasi',
        'jumlah_dibayar',
        'bukti_bayar',
        'metode_pembayaran',
        'user_id',
    ];

    protected $dates = [
        'tanggal_bayar'
    ];

    /**
     * Relasi ke Tagihan.
     */
    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    /**
     * Relasi ke User yang membuat pembayaran.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: Format jumlah dibayar ke Rupiah.
     */
    public function getJumlahDibayarFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_dibayar, 0, ',', '.');
    }

    /**
     * Booted events — auto set user_id saat create/update.
     */
    protected static function booted(): void
    {
        static::creating(function (self $pembayaran) {
            if (auth()->check()) {
                $pembayaran->user_id = auth()->id();
            }
        });

        static::updating(function (self $pembayaran) {
            if (auth()->check()) {
                $pembayaran->user_id = auth()->id();
            }
        });
    }
}
