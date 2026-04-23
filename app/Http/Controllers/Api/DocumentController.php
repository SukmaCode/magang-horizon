<?php

namespace App\Http\Controllers\Api;

use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDocumentRequest;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService
    ) {}

    public function upload(UploadDocumentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $type = DocumentType::from($validated['type']);

        // Resolve the documentable model
        $modelClass = $validated['documentable_type'];
        $documentable = $modelClass::findOrFail($validated['documentable_id']);

        $document = $this->documentService->upload(
            $request->file('file'),
            $type,
            $documentable,
            $request->user()->id
        );

        return response()->json(['message' => 'Document uploaded.', 'data' => $document], 201);
    }

    public function download(Document $document)
    {
        $fileInfo = $this->documentService->download($document);
        return response()->download($fileInfo['path'], $fileInfo['name'], [
            'Content-Type' => $fileInfo['mime'],
        ]);
    }

    public function destroy(Document $document): JsonResponse
    {
        $this->documentService->delete($document);
        return response()->json(['message' => 'Document deleted.']);
    }
}
