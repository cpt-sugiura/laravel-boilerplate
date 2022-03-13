<?php

namespace App\Http\Requests\AdminAPI\Admin;

use App\Http\Requests\BaseFormRequest;
use App\Library\Rules\UniqueInNotSoftDeleted;
use App\Models\Eloquents\Admin\Admin;

class AdminStoreRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules          = array_key_camel(Admin::createRules());
        $rules['email'] = [
            ...$rules['email'],
            new UniqueInNotSoftDeleted(new Admin(), 'email')
        ];

        return $rules;
    }

    public function attributes()
    {
        return array_key_camel(Admin::ruleAttributes());
    }
}
