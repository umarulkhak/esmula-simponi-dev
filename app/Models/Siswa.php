<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Nicolaslopezj\Searchable\SearchableTrait;

class Siswa extends Model
{
    use HasFactory, SearchableTrait;

    /**
     * Kolom yang bisa digunakan untuk pencarian.
     *
     * Bobot (score) menentukan prioritas hasil pencarian.
     */
    protected $searchable = [
        'columns' => [
            'nama' => 10,
            'nisn' => 10,
        ],
    ];

    /**
     * Kolom yang dapat diisi secara massal.
     */
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
     * Menggunakan default value jika wali belum ditentukan.
     */
    public function wali(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_id')
            ->withDefault(['name' => 'Belum ada wali murid']);
    }
}
