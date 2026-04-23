<?php

namespace App\Enums;

enum DocumentType: string
{
    case CV = 'cv';
    case AGREEMENT_INDUSTRI = 'agreement_industri';
    case AGREEMENT_MAHASISWA = 'agreement_mahasiswa';
    case SK_PEMBIMBING = 'sk_pembimbing';
    case LAPORAN = 'laporan';
    case SERTIFIKAT = 'sertifikat';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CV => 'Curriculum Vitae',
            self::AGREEMENT_INDUSTRI => 'Agreement Industri',
            self::AGREEMENT_MAHASISWA => 'Agreement Mahasiswa',
            self::SK_PEMBIMBING => 'SK Pembimbing',
            self::LAPORAN => 'Laporan Akhir',
            self::SERTIFIKAT => 'Sertifikat',
            self::OTHER => 'Dokumen Lain',
        };
    }

    /**
     * Get the storage subdirectory for this document type.
     */
    public function storagePath(): string
    {
        return match ($this) {
            self::CV => 'documents/cv',
            self::AGREEMENT_INDUSTRI => 'documents/agreements/industri',
            self::AGREEMENT_MAHASISWA => 'documents/agreements/mahasiswa',
            self::SK_PEMBIMBING => 'documents/sk',
            self::LAPORAN => 'documents/laporan',
            self::SERTIFIKAT => 'documents/sertifikat',
            self::OTHER => 'documents/other',
        };
    }

    /**
     * Max file size in kilobytes for this document type.
     */
    public function maxFileSizeKb(): int
    {
        return match ($this) {
            self::LAPORAN => 20480, // 20MB for reports
            default => 10240,       // 10MB default
        };
    }
}
