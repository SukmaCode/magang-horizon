<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSignatureRequest;
use App\Services\SignatureService;

class SignatureController extends Controller
{
    public function __construct(
        private readonly SignatureService $signatureService
    ) {}

    public function store(StoreSignatureRequest $request)
    {
        $this->signatureService->store(
            $request->user(),
            $request->validated()['signature']
        );

        return back()->with('success', 'Tanda tangan digital berhasil disimpan.');
    }
}
