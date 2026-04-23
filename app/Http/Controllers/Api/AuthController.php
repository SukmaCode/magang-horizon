<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Registration successful.',
            'user' => $user,
        ], 201);
    }

    /**
     * Login and create session.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login($request->validated());

        Auth::login($user);

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user->load('roles'),
            'redirect' => $user->role->dashboardPath(),
        ]);
    }

    /**
     * Logout and invalidate session.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Get authenticated user with profile.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'permissions']);

        // Load role-specific profile
        $user->load($user->role->value === 'mahasiswa' ? 'mahasiswa' :
            ($user->role->value === 'supervisor_industri' ? 'industri' :
            (in_array($user->role->value, ['dosen_pembimbing', 'dosen_prodi']) ? 'dosen' : [])));

        return response()->json($user);
    }
}
