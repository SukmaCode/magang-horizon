<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bimbingan extends Model
{
    use HasFactory;

    protected $fillable = [
        'magang_id',
        'tanggal',
        'catatan',
        'is_approved',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_approved' => 'boolean',
    ];

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class, 'magang_id');
    }
}
