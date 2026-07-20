<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(protected UserService $userService) {}

    /**
     * تسجيل الدخول
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials'],
            ]);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * إنشاء حساب جديد
     */
    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $this->userService->sendCustomVerificationEmail($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * توثيق البريد الإلكتروني عبر التوكن
     */
    public function verifyEmail(string $rawToken): array
    {
        $hashedToken = hash('sha256', $rawToken);

        $user = User::where('email_verification_token', $hashedToken)->first();


        if (!$user) {
            throw ValidationException::withMessages([
                'token' => ['رابط التفعيل غير صالح أو تم استخدامه مسبقاً.'],
            ]);
        }

        if ($user->verification_token_expires_at && now()->greaterThan($user->verification_token_expires_at)) {
            throw ValidationException::withMessages([
                'token' => ['انتهت صلاحية رابط التفعيل. يرجى طلب رابط جديد.'],
            ]);
        }

        $user->update([
            'email_verified_at'             => now(),
            'email_verification_token'      => null,
            'verification_token_expires_at' => null,
        ]);

        $authToken = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $authToken,
        ];
    }

    /**
     * تسجيل الخروج
     */
    public function logout(User $user): void
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->currentAccessToken();

        $token?->delete();
    }
}