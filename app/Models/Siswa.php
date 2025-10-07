<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Nicolaslopezj\Searchable\SearchableTrait;

class Siswa extends Model
{
    use HasFactory, SearchableTrait;

    protected $searchable = [
        'columns' => [
            'nama' => 10,
            'nisn' => 10,
        ],
    ];

    protected $fillable = [
        'wali_id',
        'wali_status',
        'nama',
        'nisn',
        'kelas',
        'angkatan',
        'foto',
        'user_id',
    ];

    /**
     * Relasi ke user yang membuat data siswa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke user yang menjadi wali murid.
     */
    public function wali(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_id')
            ->withDefault(['name' => 'Belum ada wali murid']);
    }

    /**
     * Relasi ke tagihan: 1 siswa punya banyak tagihan.
     */
    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'siswa_id');
    }
}
