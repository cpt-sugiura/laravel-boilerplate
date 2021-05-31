<?php

namespace App\Models\Eloquents\Member;

use App\Models\Eloquents\BaseEloquent as Model;
use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Storage;

/**
 * Class Member
 *
 * @property int                            $member_id
 * @property string                         $name                    名前
 * @property Carbon|null                    $birthday                生年月日
 * @property string|null                    $email                   メールアドレス
 * @property string                         $password                パスワード
 * @property int                            $status                  ステータス
 * @property string|null                    $auth_token              認証用トークン。ログイン時限定APIでこれが送られてない時は弾く
 * @property Carbon|null                    $last_access_at          最終アクセス日時
 * @property Carbon|null                    $created_at
 * @property Carbon|null                    $updated_at
 * @property Carbon|null                    $deleted_at
 * @property Collection|MemberDeviceToken[] $memberDeviceTokens
 * @property int|null                       $member_device_toke
 * @method static Builder|Member newModelQuery()
 * @method static Builder|Member newQuery()
 * @method static \Illuminate\Database\Query\Builder|Member onlyTrashed()
 * @method static Builder|Member query()
 * @method static Builder|Member whereAuthToken($value)
 * @method static Builder|Member whereBirthday($value)
 * @method static Builder|Member whereCreatedAt($value)
 * @method static Builder|Member whereDeletedAt($value)
 * @method static Builder|Member whereEmail($value)
 * @method static Builder|Member whereGender($value)
 * @method static Builder|Member whereLastAccessAt($value)
 * @method static Builder|Member whereMemberId($value)
 * @method static Builder|Member whereName($value)
 * @method static Builder|Member wherePassword($value)
 * @method static Builder|Member whereStatus($value)
 * @method static Builder|Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Member withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Member withoutTrashed()
 * @mixin Eloquent
 */
class Member extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use Concerns\MemberAuth;
    use Concerns\MemberRelations;
    use Concerns\MemberValidationRules;
    use MustVerifyEmail;
    use Notifiable;
    use SoftDeletes;

    public $table = 'members';

    protected $primaryKey          = 'member_id';
    public const AUTH_TOKEN_NAME   = 'auth_token';
    public const AUTH_TOKEN_LENGTH = 80;

    public const STATUS_DISABLE = 0;
    public const STATUS_ENABLE    = 1;
    public const STATUS_LIST    = [
        self::STATUS_DISABLE => '無効',
        self::STATUS_ENABLE  => '有効',
    ];

    protected static function boot(): void
    {
        parent::boot();
        self::deleting(
            static function (self $member) {
                $member->memberDeviceTokens()->delete();
            }
        );
    }

    public const STORAGE_DISK_KEY = 'member_private';

    /**
     * 会員用ファイルストレージを返す
     * @return FilesystemAdapter
     */
    public static function storage(): FilesystemAdapter
    {
        return Storage::disk(self::STORAGE_DISK_KEY);
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public $guarded = [
        'password', // fill使用による平文誤混入を防ぎ、プロパティ直指定で代入することを期待
        'auth_token',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'last_access_at',
    ];

    /**
     * 管理者用の Guarded をセット。
     */
    public function setGuardedInAdmin(): void
    {
        $this->guarded = array_filter(
            $this->guarded,
            static function ($guard) {
                $ignoreGuardsInAdmin = [
                    'status',
                ];

                return ! in_array($guard, $ignoreGuardsInAdmin, true);
            }
        );
    }

    protected $hidden = [
        'auth_token',
        'password'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'member_id'      => 'integer',
        'name'           => 'string',
        'gender'         => 'integer',
        'status'         => 'integer',
        'birthday'       => 'date',
        'email'          => 'string',
        'password'       => 'string',
        'auth_token'     => 'string',
        'last_access_at' => 'datetime',
    ];

    public function getDeviceTokens(string $controlColumn): array
    {
        return $this->memberDeviceTokens()
            ->where($controlColumn, '=', MemberDeviceToken::PUSH_FULL)
            ->get()
            ->map(fn ($t) => $t->device_token)
            ->flatten()
            ->unique()
            ->toArray();
    }
}
