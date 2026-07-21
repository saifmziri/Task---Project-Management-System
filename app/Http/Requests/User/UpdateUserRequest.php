<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->user()?->id;

        return [
            'full_name'    => ['sometimes', 'required', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'required', 'string', 'max:20'],
            'email'        => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
        ];
    }
}