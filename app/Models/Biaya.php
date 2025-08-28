<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biaya extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

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
