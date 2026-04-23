<?php

namespace App\Services;

use App\Models\Signature;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SignatureService
{
    /**
     * Store a digital signature from base64 canvas data.
     */
    public function store(User $user, string $base64Data): Signature
    {
        // Decode base64 image
        $imageData = $this->decodeBase64Image($base64Data);

        // Generate unique filename
        $filename = "signatures/{$user->id}/" . uniqid('sig_') . '.png';

        // Store in private disk
        Storage::disk('private')->put($filename, $imageData);

        // Create signature record
        $signature = Signature::create([
            'user_id' => $user->id,
            'signature_image_path' => $filename,
            'signed_at' => now(),
        ]);

        activity('signature')
            ->performedOn($signature)
            ->causedBy($user)
            ->log('Digital signature stored');

        return $signature;
    }

    /**
     * Get the latest signature for a user.
     */
    public function getLatest(User $user): ?Signature
    {
        return $user->signatures()->latest('signed_at')->first();
    }

    /**
     * Delete a signature.
     */
    public function delete(Signature $signature): bool
    {
        Storage::disk('private')->delete($signature->signature_image_path);
        return $signature->delete();
    }

    /**
     * Decode base64 image data (strip data URI prefix if present).
     */
    private function decodeBase64Image(string $base64Data): string
    {
        // Remove data URI scheme if present (e.g., "data:image/png;base64,")
        if (str_contains($base64Data, ',')) {
            $base64Data = explode(',', $base64Data, 2)[1];
        }

        $decoded = base64_decode($base64Data, true);

        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid base64 image data.');
        }

        return $decoded;
    }
}
