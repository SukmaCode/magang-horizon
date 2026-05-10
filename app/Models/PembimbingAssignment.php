<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PembimbingAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'magang_aktif_id',
        'dosen_id',
        'assigned_by',
    ];

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function suratKeputusan(): HasOne
    {
        return $this->hasOne(SuratKeputusanPembimbing::class, 'assignment_id');
    }
}
