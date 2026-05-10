<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratKeputusanPembimbing extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'nomor_sk',
        'tanggal_sk',
        'file_sk',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(PembimbingAssignment::class, 'assignment_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
