<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * تحديث بيانات مستخدم
     */
    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    /**
     * تغيير حالة المستخدم مع الفحص الذكي
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