<?php

namespace App\Models\Eloquents\Member;

use App\Models\Eloquents\BaseEloquent as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\In;

/**
 * Class MemberDeviceToken
 *
 * @property int         $member_device_token_id
 * @property int         $member_id
 * @property string      $device_token                     firebase のデバイストークン
 * @property int         $push_some_notify                 何かを受け取った時の制御
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Member      $member
 * @method static Builder|MemberDeviceToken newModelQuery()
 * @method static Builder|MemberDeviceToken newQuery()
 * @method static Builder|MemberDeviceToken query()
 * @method static Builder|MemberDeviceToken whereCreatedAt($value)
 * @method static Builder|MemberDeviceToken whereDeviceToken($value)
 * @method static Builder|MemberDeviceToken whereMemberDeviceTokenId($value)
 * @method static Builder|MemberDeviceToken whereMemberId($value)
 * @method static Builder|MemberDeviceToken wherePushSomeNotify($value)
 * @method static Builder|MemberDeviceToken whereUpdatedAt($value)
 * @mixin Eloquent
 */
class MemberDeviceToken extends Model
{
    public $table = 'member_device_tokens';

    protected $primaryKey = 'member_device_token_id';

    public $fillable = [
        'device_token',
        'push_some_notify',
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(
            static function (self $deviceToken) {
                $deviceToken->push_some_notify ??= self::PUSH_FULL;
            }
        );
    }

    /**
     * @var array プッシュ制御で使っているカラムの名前
     */
    public static $pushControlColumns = [
        'push_some_notify'
    ];

    public const PUSH_NONE = 0;
    public const PUSH_FULL = 1;
    public const PUSH_STATUS = [
        self::PUSH_NONE => 'プッシュしない',
        self::PUSH_FULL => 'プッシュする',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'member_device_token_id'      => 'integer',
        'application_user_company_id' => 'integer',
        'member_id'                   => 'integer',
        'device_token'                => 'string',
        'push_some_notify'            => 'integer',
    ];

    public static function rules(): array
    {
        return [
            'device_token'     => ['string'],
            'push_some_notify' => ['nullable', 'integer', new In(array_keys(self::PUSH_STATUS))],
        ];
    }

    /**
     * @return BelongsTo
     **/
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public static function ruleAttributes(): array
    {
        return [
            'device_token'     => 'firebase のデバイストークン',
            'push_some_notify' => '何かの時のプッシュ通知制御',
        ];
    }
}
