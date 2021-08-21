<?php

namespace App\Models\Eloquents;

use App\Models\Eloquents\BaseEloquent as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class AdminPasswordReminderToken
 *
 * @property string      $email
 * @property string      $token
 * @property Carbon|null $created_at
 * @method static Builder|AdminPasswordResetToken newModelQuery()
 * @method static Builder|AdminPasswordResetToken newQuery()
 * @method static Builder|AdminPasswordResetToken query()
 * @method static Builder|AdminPasswordResetToken whereApplicationUserCompanyId($value)
 * @method static Builder|AdminPasswordResetToken whereCreatedAt($value)
 * @method static Builder|AdminPasswordResetToken whereEmail($value)
 * @method static Builder|AdminPasswordResetToken whereToken($value)
 * @mixin Eloquent
 */
class AdminPasswordResetToken extends Model
{
    public $table = 'admin_password_reset_tokens';

    public $fillable = [
        'email',
        'token'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'email' => 'string',
        'token' => 'string'
    ];
}
