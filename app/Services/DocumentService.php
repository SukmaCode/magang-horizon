<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DocumentService
{
    public function upload(UploadedFile $file, DocumentType $type, Model $documentable, int $uploadedBy): Document
    {
        $this->validateFileType($file);
        $this->validateFileSize($file, $type);

        $path = $file->store($type->storagePath().'/'.$documentable->getKey(), 'private');

        $document = Document::create([
            'documentable_type' => get_class($documentable),
            'documentable_id' => $documentable->getKey(),
            'type' => $type,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $uploadedBy,
        ]);

        activity('document')->performedOn($document)
            ->withProperties(['type' => $type->value, 'original_name' => $file->getClientOriginalName()])
            ->log("Document uploaded: {$type->label()}");

        return $document;
    }

    public function download(Document $document): array
    {
        if (! Storage::disk('private')->exists($document->file_path)) {
            throw new \Exception('File not found in storage.');
        }

        return [
            'path' => Storage::disk('private')->path($document->file_path),
            'name' => $document->original_name,
            'mime' => $document->mime_type,
        ];
    }

    public function delete(Document $document): bool
    {
        Storage::disk('private')->delete($document->file_path);
        activity('document')->performedOn($document)->log("Document deleted: {$document->type->label()}");
        return $document->delete();
    }

    public function getForModel(Model $model, ?DocumentType $type = null)
    {
        $query = $model->documents();
        if ($type) { $query->where('type', $type); }
        return $query->latest()->get();
    }

    private function validateFileType(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), ['application/pdf'])) {
            throw ValidationException::withMessages(['file' => ['Only PDF files are allowed.']]);
        }
    }

    private function validateFileSize(UploadedFile $file, DocumentType $type): void
    {
        $maxKb = $type->maxFileSizeKb();
        if (($file->getSize() / 1024) > $maxKb) {
            throw ValidationException::withMessages(['file' => ["File exceeds max size of {$maxKb}KB."]]);
        }
    }
}
