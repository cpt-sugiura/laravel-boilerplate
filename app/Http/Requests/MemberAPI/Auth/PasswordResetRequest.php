<?php

namespace App\Http\Requests\MemberAPI\Auth;

use App\Http\Requests\BaseFormRequest;
use App\Models\Eloquents\Member\Member;

/**
 * Class PasswordResetRequest
 * @package App\Http\Requests\Auth
 * @property string $token
 * @property string $email
 * @property string $password
 * @property string $passwordConfirm
 */
class PasswordResetRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'token'           => 'required',
            'email'           => ['required', 'string'],
            'password'        => ['required', ...Member::passwordRule()],
            'passwordConfirm' => ['required', 'string', 'same:password'],
        ];
    }
}
