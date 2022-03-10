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
        /**
         * バリデーションルールを表現する配列ないし文字列にrequiredルールを追加する.
         * nullableが定義されている場合は追加をしない
         * @param  array|string  $rule
         * @return array|string
         */
        function before_insert_required_rule(array|string $rule): array|string
        {
            /* @var string|array $rule */
            if (is_string($rule) && !str_contains($rule, 'nullable')) {
                $rule = 'required|'.$rule;
            } elseif (is_array($rule) && ! in_array('nullable', $rule, true)) {
                $rule = array_merge(['required'], $rule);
            }

            return $rule;
        }

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
