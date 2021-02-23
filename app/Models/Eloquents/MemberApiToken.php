<?php

namespace App\Models\Eloquents;

use App\Models\Eloquents\BaseEloquent as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Str;

/**
 * Class ApiToken
 *
 * @property int                         $id
 * @property int                         $application_user_company_id
 * @property string                      $token APIトークン. これ抜きでAPIが叩かれたら通信拒否
 * @property Carbon|null                 $created_at
 * @property Carbon|null                 $updated_at
 * @property ApplicationUserCompany $applicationUserCompany
 * @method static Builder|MemberApiToken newModelQuery()
 * @method static Builder|MemberApiToken newQuery()
 * @method static Builder|MemberApiToken query()
 * @method static Builder|MemberApiToken whereApplicationUserCompanyId($value)
 * @method static Builder|MemberApiToken whereCreatedAt($value)
 * @method static Builder|MemberApiToken whereId($value)
 * @method static Builder|MemberApiToken whereToken($value)
 * @method static Builder|MemberApiToken whereUpdatedAt($value)
 * @mixin Eloquent
 */
class MemberApiToken extends Model
{
    public $table = 'member_api_tokens';

    protected static function boot()
    {
        parent::boot();
        /*
         * 特別指定なくDBに保存する際には自動でAPIトークン用のランダム文字列を生成
         */
        self::saving(
            static function (self $apiToken) {
                $apiToken->token = $apiToken->token ?? self::makeToken();
            }
        );
    }

    public static function makeToken(): string
    {
        return Str::random(32);
    }

    public $fillable = [
        'application_user_company_id',
        'token'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                          => 'integer',
        'application_user_company_id' => 'integer',
        'token'                       => 'string'
    ];

    /**
     * Validation rules
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'application_user_company_id' => ['integer', 'between:0,18446744073709551615'],
            'token'                       => ['string', 'max:255'],
        ];
    }

    /**
     * @return BelongsTo
     **/
    public function applicationUserCompany(): BelongsTo
    {
        return $this->belongsTo(ApplicationUserCompany::class, 'application_user_company_id');
    }

    public static function ruleAttributes(): array
    {
        return [
              'application_user_company_id' => '',
              'token'                       => 'APIトークン. これ抜きでAPIが叩かれたら通信拒否',
        ];
    }
}
