<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ChangePasswordRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.confirmed' => 'تأكيد كلمة المرور الجديدة غير مطابق.',
            'new_password.different' => 'كلمة المرور الجديدة يجب أن تكون مختلفة عن كلمة المرور الحالية.',
            'new_password.min' => 'كلمة المرور الجديدة يجب أن لا تقل عن 8 خانات.',
        ];
    }
}