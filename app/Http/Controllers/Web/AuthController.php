<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Handle registration.
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

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
