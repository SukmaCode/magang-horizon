<?php

namespace App\Models;

use App\Enums\StatusAgreement;
use App\Enums\StatusTahapan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MagangAktif extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'pendaftaran_id',
        'file_agreement_industri',
        'file_agreement_mahasiswa',
        'status_agreement',
        'alasan_tolak_agreement',
        'sk_pembimbing_path',
        'supervisor_kampus_id',
        'supervisor_industri_id',
        'status_tahapan',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'status_tahapan' => StatusTahapan::class,
            'status_agreement' => StatusAgreement::class,
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status_tahapan', 'supervisor_kampus_id', 'supervisor_industri_id', 'tanggal_mulai', 'tanggal_selesai'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Magang Aktif #{$this->id} was {$eventName}");
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function supervisorKampus(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'supervisor_kampus_id');
    }

    public function supervisorIndustri(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_industri_id');
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class, 'magang_id');
    }

    public function laporanAkhir(): HasOne
    {
        return $this->hasOne(LaporanAkhir::class, 'magang_id');
    }

    public function penilaian(): HasOne
    {
        return $this->hasOne(Penilaian::class, 'magang_id');
    }

    public function sertifikat(): HasOne
    {
        return $this->hasOne(Sertifikat::class, 'magang_id');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function industri(): BelongsTo
    {
        return $this->belongsTo(Industri::class, 'user_id'); 
    }
    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Get the student (mahasiswa) through the pendaftaran relationship.
     */
    public function getMahasiswaAttribute(): ?Mahasiswa
    {
        return $this->pendaftaran?->mahasiswa;
    }

    /**
     * Get the industry through the pendaftaran relationship.
     */
    public function getIndustriAttribute(): ?Industri
    {
        return $this->pendaftaran?->industri;
    }

    /**
     * Check if supervisors have been assigned.
     */
    public function hasSupervisorsAssigned(): bool
    {
        return $this->supervisor_kampus_id !== null && $this->supervisor_industri_id !== null;
    }

    /**
     * Check if agreements have been uploaded AND accepted by student.
     */
    public function hasAgreementsUploaded(): bool
    {
        return $this->file_agreement_industri !== null
            && $this->status_agreement === StatusAgreement::ACCEPTED;
    }

    /**
     * Check if the agreement was rejected by the student.
     */
    public function isAgreementRejected(): bool
    {
        return $this->status_agreement === StatusAgreement::REJECTED;
    }

    /**
     * Check if the internship can transition to pelaksanaan.
     */
    public function canStartExecution(): bool
    {
        return $this->hasSupervisorsAssigned()
            && $this->hasAgreementsUploaded()
            && $this->status_tahapan === StatusTahapan::PERSIAPAN;
    }
}
