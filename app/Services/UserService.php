<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * تحديث بيانات المستخدم
     */
    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // فقط تحديث البيانات، بدون إرسال verification (تم نقل auth logic إلى AuthService)
        $user->update($data);

        return $user;
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