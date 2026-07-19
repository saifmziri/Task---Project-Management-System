<?php

namespace App\Services;

use App\Models\User;
use App\Mail\VerifyEmail; // استدعاء كلاس الإيميل الذي قمنا بتعديله
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class AuthService
{
    /**
     * معالجة عملية تسجيل الدخول وتوليد التوكن.
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
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * معالجة عملية التسجيل، إنشاء مستخدم جديد، وإرسال إيميل التحقق.
     */
    public function register(array $data): array
    {
        // 1. تشفير كلمة المرور وإنشاء الحساب
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        // 2. توليد توكن عشوائي خاص بالتحقق من الإيميل
        $emailToken = Str::random(64);
        
        // قم بتحديث حقل التوكن في قاعدة البيانات للمستخدم
        $user->update(['email_verification_token' => $emailToken]); 

        // 3. بناء الرابط الموجه إلى فرونتند الرياكت (React)
        // يتم جلب الرابط من ملف الـ .env (مثال: http://localhost:5173)
        $reactUrl = env('FRONTEND_URL', 'http://localhost:5173') . "/verify-email?token=" . $emailToken;

        // 4. إرسال الإيميل وتمرير كائن المستخدم والرابط داخل الـ Constructor هنا!
        Mail::to($user->email)->send(new VerifyEmail($user, $reactUrl));

        // 5. توليد توكن صلاحية الدخول (Sanctum) ليعود إلى الـ Controller
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function verifyEmail(string $token): array
    {
        // 1. البحث عن المستخدم الذي يمتلك هذا التوكن الخاص بالإيميل
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'token' => ['رابط التفعيل غير صالح أو انتهت صلاحيته.'],
            ]);
        }

        // 2. تحديث الحساب ليصبح مفعلاً وتصفير التوكن لحمايته
        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null // نمسحه لكي لا يُستخدم الرابط مرة أخرى
        ]);

        // 3. توليد توكن دخول جديد (Sanctum) ليعود إلى الـ Controller
        $authToken = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $authToken,
        ];
    }

    /**
     * معالجة تسجيل الخروج وحذف التوكن الحالي.
     */
    public function logout(User $user): void
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->currentAccessToken();
    
        $token->delete();
    }
}