<?php

namespace App\Http\Requests\MemberAPI\Auth;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string    $email
 * @property string    $password
 * @property bool|null $remember
 */
class LoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email'            => ['required', 'string'],
            'password'         => ['required', 'string'],
            'remember'         => ['nullable', 'boolean'],
        ];
    }
}
