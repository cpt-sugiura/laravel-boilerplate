<?php

namespace App\Http\Requests\AdminAPI\Admin;

use App\Http\Requests\BaseFormRequest;
use App\Library\Rules\UniqueInNotSoftDeleted;
use App\Models\Eloquents\Admin\Admin;

class AdminUpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules          = array_key_camel(Admin::updateRules());
        $rules['email'] = [
            ...$rules['email'],
            new UniqueInNotSoftDeleted(new Admin(), 'email', request()->adminId)
        ];
        $rules['password']        = ['nullable', ...$rules['password']];
        $rules['passwordConfirm'] = ['nullable', ...$rules['passwordConfirm']];

        return $rules;
    }

    public function attributes()
    {
        return array_key_camel(Admin::ruleAttributes());
    }
}
