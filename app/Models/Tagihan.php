<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tagihan extends Model
{
    use HasFactory;

    // ✅ WAJIB: Tambahkan ini!
    protected $table = 'tagihans'; // ← Sesuaikan dengan nama tabel di database

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
}
