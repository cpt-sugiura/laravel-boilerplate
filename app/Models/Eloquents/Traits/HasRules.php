<?php

namespace App\Models\Eloquents\Traits;

use App\Models\Eloquents\Contracts\HasRulesContract;
use Arr;

/**
 * HasRulesContractを満たすクラスのためのTrait
 * @see     HasRulesContract
 * Trait HasRules
 * @package App\Models\Eloquents\Traits
 * @mixin HasRulesContract
 */
trait HasRules
{
    /**
     * 作成時のルール. nullableでないカラムはいずれもrequired.
     * @return array
     */
    public static function createRules(): array
    {
        return collect(static::rules())->map(
            static function ($rule) {
                return before_insert_required_rule($rule);
            }
        )->toArray();
    }

    /**
     * 更新時のルール. 一度レコードに入力されたら不変の値を触ってはいけない
     * @return array
     */
    public static function updateRules(): array
    {
        return Arr::except(static::rules(), static::createOnlyRuleColumns());
    }

    /**
     * レコード作成時にのみ適用するルールを持つカラム名を定義する
     * @return array
     */
    public static function createOnlyRuleColumns(): array
    {
        return [];
    }

    /**
     * ルールの自然言語名定義
     * @return array
     */
    public static function ruleAttributes(): array
    {
        return [];
    }
}
