<?php

namespace App\Services;

use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthService
{
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

        // منع الدخول قبل تفعيل البريد
        if (is_null($user->email_verified_at)) {
            throw ValidationException::withMessages([
                'email' => ['Please verify your email before logging in.'],
            ]);
        }

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

        $this->sendCustomVerificationEmail($user);

        // لا ننشئ token هنا، فقط بعد verifyEmail
        return [
            'user' => $user,
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

        // تنظيف أي توكنات قديمة قبل إصدار جديد
        $user->tokens()->delete();

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

    /**
     * توليد توكن التفعيل وإرسال الإيميل
     */
    public function sendCustomVerificationEmail(User $user): void
    {
        $rawToken = Str::random(64);

        $user->update([
            'email_verification_token'      => hash('sha256', $rawToken),
            // وحّدها حسب سياستك (10 دقائق أو 24 ساعة)
            'verification_token_expires_at' => now()->addMinutes(10),
        ]);

        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $reactUrl = "{$frontendUrl}/verify-email?token={$rawToken}";

        Mail::to($user->email)->send(new VerifyEmail($user, $reactUrl));
    }

    /**
     * إعادة إرسال رابط التفعيل
     */
    public function resendVerificationEmail(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user || $user->email_verified_at !== null) {
            return;
        }

        $this->sendCustomVerificationEmail($user);
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(User $user, array $data): void
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['كلمة المرور الحالية غير صحيحة.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        // أمنيًا: تسجيل خروج كل الأجهزة بعد تغيير كلمة المرور
        $user->tokens()->delete();
    }
}