<?php

namespace App\Services;

use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class UserService
{
    /**
     * توليد توكن التفعيل وإرسال الإيميل
     */
    public function sendCustomVerificationEmail(User $user): void
    {
        $rawToken = Str::random(64);

        // 🎯 تم إضافة صلاحية للتوكن (مثلاً 24 ساعة) لربطها بـ verifyEmail
        $user->update([
            'email_verification_token' => hash('sha256', $rawToken),
            'verification_token_expires_at' => now()->addMinutes(10), 
        ]);

        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $reactUrl = "{$frontendUrl}/verify-email?token={$rawToken}";

        Mail::to($user->email)->send(new VerifyEmail($user, $reactUrl));
    }

    /**
     * إعادة إرسال رابط التفعيل بحال انتهت الصلاحية أو لم يصل الإيميل
     */
    public function resendVerificationEmail(string $email): void
    {
        $user = User::where('email', $email)->first();
    
        // إذا لم يكن المستخدم موجوداً أو كان حسابه موثقاً بالفعل، لا نفعل شيئاً (أو نرجع استجابة)
        if (!$user || $user->email_verified_at !== null) {
            return;
        }
    
        // توليد توكن جديد وإرساله
        $this->sendCustomVerificationEmail($user);
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function updateUser(User $user, array $data): User
    {
        // 1. معالجة كلمة المرور
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // 2. التحقق من تغير الإيميل
        $emailChanged = isset($data['email']) && $data['email'] !== $user->email;

        if ($emailChanged) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        // 3. إرسال الإيميل في حال تغييره
        if ($emailChanged) {
            $this->sendCustomVerificationEmail($user);
        }

        return $user;
    }

    public function changePassword(User $user, array $data): void
    {
        // 1. التحقق من أن كلمة المرور الحالية صحيحة
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['كلمة المرور الحالية غير صحيحة.'],
            ]);
        }
    
        // 2. تحديث كلمة المرور بالجديدة المشفّرة
        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);
    }

    /**
     * تغيير حالة المستخدم
     */
    public function changeStatus(User $user, string $status): ?User
    {
        if ($user->status === $status) {
            return null; 
        }

        $user->update(['status' => $status]);
        return $user;
    }

    /**
     * حذف مستخدم
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
}