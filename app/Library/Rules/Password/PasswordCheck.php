<?php

namespace App\Library\Rules\Password;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\Rule;

class PasswordCheck implements Rule
{
    protected EloquentUserProvider $provider;
    protected Authenticatable $authModel;

    public function __construct(EloquentUserProvider $provider, Authenticatable $authModel)
    {
        $this->provider  = $provider;
        $this->authModel = $authModel;
    }

    public function passes($attribute, $value): bool
    {
        return $this->provider->getHasher()->check($value, $this->authModel->getAuthPassword());
    }

    public function message(): string
    {
        return 'パスワードが誤っています。';
    }

    public function __toString(): string
    {
        return 'パスワードチェック';
    }
}
