<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tagihan extends Model
{
    use HasFactory;

    protected $fillable = [
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
     * Get the user that owns the Tagihan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns the Tagihan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function formatRupiah($field = 'jumlah')
    {
        $value = $this->$field ?? 0;
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
