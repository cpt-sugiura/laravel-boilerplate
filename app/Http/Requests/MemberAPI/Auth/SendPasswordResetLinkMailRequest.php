<?php

namespace App\Http\Requests\MemberAPI\Auth;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $email
 * @property string $orderNumber
 */
class SendPasswordResetLinkMailRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email'       => ['required', 'string'],
            'orderNumber' => ['required', 'string'],
        ];
    }
}
