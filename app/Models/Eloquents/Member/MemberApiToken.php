<?php

namespace App\Models\Eloquents\Member;

use App\Models\Eloquents\BaseEloquent as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Str;

/**
 * Class ApiToken
 *
 * @property int                         $id
 * @property string                      $token APIトークン. これ抜きでAPIが叩かれたら通信拒否
 * @property Carbon|null                 $created_at
 * @property Carbon|null                 $updated_at
 * @method static Builder|MemberApiToken newModelQuery()
 * @method static Builder|MemberApiToken newQuery()
 * @method static Builder|MemberApiToken query()
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
        'token'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                          => 'integer',
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
            'token'                       => ['string', 'max:255'],
        ];
    }

    public static function ruleAttributes(): array
    {
        return [
              'token'                       => 'APIトークン. これ抜きでAPIが叩かれたら通信拒否',
        ];
    }
}
