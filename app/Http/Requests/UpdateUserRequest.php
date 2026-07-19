<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // تفعيل الصلاحية
    }

    public function rules(): array
    {
        // جلب معرف المستخدم من المسار (في حال أدمن يعدل على مستخدم، أو نأخذ الحالي)
        $userId = $this->route('id') ?? $this->user()?->id;

        return [
            'full_name'    => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:20',
            'email'        => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'password'     => 'sometimes|nullable|string|min:8|confirmed',
        ];
    }
}