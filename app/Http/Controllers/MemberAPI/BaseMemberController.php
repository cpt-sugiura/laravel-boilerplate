<?php

namespace App\Http\Controllers\MemberAPI;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Eloquents\Member;

abstract class BaseMemberController extends Controller
{
    use ApiResponseTrait;

    protected function loginMember(): Member
    {
        /** @var Member|null $member */
        $member = auth('member_api')->user();

        return $member;
    }
}
