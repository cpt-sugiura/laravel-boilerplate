<?php

namespace App\Http\Controllers\MemberAPI;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Eloquents\Member\Member;
use Auth;
use Illuminate\Auth\SessionGuard;

abstract class BaseMemberController extends Controller
{
    use ApiResponseTrait;

    protected function guard(): SessionGuard
    {
        return Auth::guard('web');
    }

    protected function loginUser(): ?Member
    {
        $user = Auth::guard('web')->user();
        if ($user instanceof Member) {
            return $user;
        }

        return null;
    }
}
