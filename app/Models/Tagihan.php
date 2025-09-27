<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihans';

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
        'created_at',
        'updated_at',
    ];

    protected $with = [
        'user',
        'siswa',
        'tagihanDetails',
    ];

    public function scopeWaliSiswa($query)
    {
        // Filter hanya data yang terkait dengan semua siswa yang dimiliki oleh user yang login
        return $query->whereIn('siswa_id', Auth::user()->getAllSiswaId());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tagihanDetails(): HasMany
    {
        return $this->hasMany(TagihanDetail::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Accessor: Tampilkan status tagihan dalam bahasa yang ramah untuk wali murid.
     * Contoh: "lunas" → "Sudah dibayar"
     */
    public function getStatusTagihanWaliAttribute(): string
    {
        return match ($this->status) {
            'baru'  => 'Belum dibayar',
            'lunas' => 'Sudah dibayar',
            default => ucfirst($this->status),
        };
    }
}
