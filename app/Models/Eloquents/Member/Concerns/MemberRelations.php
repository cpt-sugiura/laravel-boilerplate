<?php

namespace App\Models\Eloquents\Member\Concerns;

use App\Models\Eloquents\Member\MemberDeviceToken;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait MemberRelations
{
    /**
     * @return HasMany
     **/
    public function memberDeviceTokens(): HasMany
    {
        return $this->hasMany(MemberDeviceToken::class, 'member_id');
    }
}
