<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\OtpResetPasswordMail;
use Illuminate\Validation\ValidationException;

class OtpResetController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Alamat email tidak ditemukan dalam sistem kami.'
        ]);

        // Generate 6-digit OTP
        $otp = (string) rand(100000, 999999);
        $email = $request->email;

        // Store OTP in cache for 5 minutes
        Cache::put('otp_reset_' . $email, $otp, now()->addMinutes(5));

        // Send email
        Mail::to($email)->send(new OtpResetPasswordMail($otp));

        return back()->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $email = $request->email;
        $otp = $request->otp;

        $cachedOtp = Cache::get('otp_reset_' . $email);

        if (!$cachedOtp || $cachedOtp !== $otp) {
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        // OTP is valid. Return success so frontend can move to step 3.
        return back()->with('success', 'OTP valid. Silakan masukkan password baru.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $email = $request->email;
        $otp = $request->otp;

        // Double check OTP to be secure
        $cachedOtp = Cache::get('otp_reset_' . $email);

        if (!$cachedOtp || $cachedOtp !== $otp) {
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        // Update password
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear OTP from cache
        Cache::forget('otp_reset_' . $email);

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
