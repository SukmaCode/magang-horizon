<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\OtpRegisterMail;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Show login page.
     */
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Show register page.
     */
    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle login.
     */
    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request->validated());

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended($user->role->dashboardPath());
    }

    /**
     * Send OTP for registration.
     */
    public function sendRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $otp = (string) rand(100000, 999999);
        $email = $request->email;

        Cache::put('otp_register_' . $email, $otp, now()->addMinutes(5));

        Mail::to($email)->send(new OtpRegisterMail($otp));

        return back()->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Handle registration.
     */
    public function register(RegisterRequest $request)
    {
        $email = $request->email;
        $otp = $request->otp;

        $cachedOtp = Cache::get('otp_register_' . $email);

        if (!$cachedOtp || $cachedOtp !== $otp) {
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $user = $this->authService->register($request->validated());

        // Bersihkan cache OTP setelah berhasil registrasi
        Cache::forget('otp_register_' . $email);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->role->dashboardPath());
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
