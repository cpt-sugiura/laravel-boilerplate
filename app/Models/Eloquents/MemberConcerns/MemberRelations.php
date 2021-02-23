<?php

namespace App\Models\Eloquents\MemberConcerns;

use App\Models\Eloquents\MemberDeviceToken;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
