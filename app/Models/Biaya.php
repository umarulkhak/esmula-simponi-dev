<?php

namespace App\Models;

use App\Traits\HasFormatRupiah;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Casts\Attribute;

class Biaya extends Model
{
    use HasFactory;
    use HasFormatRupiah;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    protected $append = ['nama_biaya_full'];

    /**
     * Get the full biaya name with formatted jumlah.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function namaBiayaFull(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->nama . ' - ' . $this->formatRupiah('jumlah'),
        );
    }


    /**
     * Get the user that owns the biaya.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Handle model events.
     */
    protected static function booted(): void
    {
        static::creating(fn ($biaya) => $biaya->user_id = auth()->id());
        static::updating(fn ($biaya) => $biaya->user_id = auth()->id());
    }
}
