<?php

namespace App\UseCase\AdminBrowserAPI\Auth;

use App\Models\Eloquents\Admin\Admin;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;

class AdminAuthProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array      $credentials
     * @return Admin|null
     */
    public function retrieveByCredentials(array $credentials): ?Admin
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                Str::contains($this->firstCredentialKey($credentials), 'password'))) {
            return null;
        }

        $email = $credentials['email'];

        /** @var Admin $admin */
        $admin = Admin::where(static function ($query) use ($email) {
            return $query->where('email', '<>', '')
                    ->whereNotNull('email')
                    ->where('email', '=', $email);
        })->first();

        return $admin;
    }
}
