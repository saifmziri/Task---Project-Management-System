<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class ChangeUserStatusRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'The status field is required.',
            'status.in'       => 'The status must be either active or inactive.',
        ];
    }
}