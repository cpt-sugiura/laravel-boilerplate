<?php

namespace App\UseCase\MemberAuth;

use App\Models\Eloquents\Member;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;

class MemberAuthProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array        $credentials
     * @return Member|null
     */
    public function retrieveByCredentials(array $credentials): ?Member
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                Str::contains($this->firstCredentialKey($credentials), 'password'))) {
            return null;
        }

        $email = $credentials['email'];

        /** @var Member $member */
        $member = Member::whereEmail($email)
            ->whereStatus(Member::STATUS_ENABLE)
            ->first();

        return $member;
    }
}
