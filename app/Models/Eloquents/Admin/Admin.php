<?php

namespace App\Models\Eloquents\Admin;

use App\Models\Eloquents\BaseEloquent as Model;
use App\Models\Eloquents\Traits\HasRules;
use Auth;
use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Storage;
use Str;

/**
 * Class Admin
 *
 * @property int         $admin_id
 * @property string      $name           名前
 * @property string|null $email          メールアドレス
 * @property string      $password       パスワード
 * @property string      $remember_token 継続ログイン用トークン
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder|Admin newModelQuery()
 * @method static Builder|Admin newQuery()
 * @method static \Illuminate\Database\Query\Builder|Admin onlyTrashed()
 * @method static Builder|Admin query()
 * @method static Builder|Admin whereAdminId($value)
 * @method static Builder|Admin whereCreatedAt($value)
 * @method static Builder|Admin whereDeletedAt($value)
 * @method static Builder|Admin whereEmail($value)
 * @method static Builder|Admin whereName($value)
 * @method static Builder|Admin wherePassword($value)
 * @method static Builder|Admin whereRememberToken($value)
 * @method static Builder|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Admin withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Admin withoutTrashed()
 * @mixin Eloquent
 */
class Admin extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use HasRules;
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use MustVerifyEmail;
    use Notifiable;
    use SoftDeletes;

    public $table = 'admins';

    protected $primaryKey = 'admin_id';

    protected static function boot()
    {
        parent::boot();
        self::saving(fn ($admin) => $admin->remember_token = $admin->remember_token ?? Str::random(32));
    }

    public const ASSETS_STORAGE_DISK_KEY = 'admin_auth_assets_storage';

    /**
     * 管理者用ファイルストレージを返す
     * @return FilesystemAdapter
     */
    public function assetStorage(): FilesystemAdapter
    {
        return Storage::disk(self::ASSETS_STORAGE_DISK_KEY);
    }

    public $fillable = [
        'name',
        'email',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'admin_id'                    => 'integer',
        'name'                        => 'string',
        'email'                       => 'string',
        'password'                    => 'string',
        'remember_token'              => 'string',
    ];

    /**
     * Validation rules
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name'            => ['string', 'max:255'],
            'email'           => ['nullable', 'string', 'max:255', 'email:rfc'],
            'password'        => ['string', 'max:255', 'min:8'],
            'passwordConfirm' => ['string', 'same:password'],
        ];
    }

    public static function ruleAttributes(): array
    {
        return [
            'name'            => '名前',
            'email'           => 'メールアドレス',
            'password'        => 'パスワード',
            'passwordConfirm' => 'パスワード（確認用）',
        ];
    }

    /**
     * 使っている認証用プロバイダを返す
     * @return EloquentUserProvider
     */
    public static function getAuthProvider(): EloquentUserProvider
    {
        /** @var SessionGuard $guard */
        $guard = Auth::guard('admin_web');
        /** @var EloquentUserProvider $provider */
        $provider = $guard->getProvider();

        return $provider;
    }

    /**
     * 管理者に紐づいた認証機能からパスワードハッシュを生成
     * @param  string $password
     * @return string
     */
    public static function makePassword(string $password): string
    {
        return self::getAuthProvider()->getHasher()->make($password);
    }

    /**
     * パスワードリセット通知の送信をオーバーライド
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(
            new AdminPasswordResetNotification(
                $token,
                now()->addMinutes(config('auth.passwords.users.expire')),
                $this->name
            )
        );
    }
}
