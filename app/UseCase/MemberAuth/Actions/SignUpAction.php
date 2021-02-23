<?php

namespace App\UseCase\MemberAuth\Actions;

use App\Models\Eloquents\Member;

class SignUpAction
{
    public function __invoke(Member $member)
    {
        assert(! $member->exists);
        // なんか保存処理
    }
}
