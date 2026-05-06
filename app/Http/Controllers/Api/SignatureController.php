<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSignatureRequest;
use App\Services\SignatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function __construct(
        private readonly SignatureService $signatureService
    ) {}

    public function store(StoreSignatureRequest $request): JsonResponse
    {
        $signature = $this->signatureService->store(
            $request->user(),
            $request->validated()['signature']
        );

        return response()->json(['message' => 'Signature saved.', 'data' => $signature], 201);
    }

    public function latest(Request $request): JsonResponse
    {
        $signature = $this->signatureService->getLatest($request->user());

        return response()->json($signature);
    }
}
