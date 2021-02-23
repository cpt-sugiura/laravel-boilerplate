<?php

namespace App\Models\Eloquents\Contracts;

interface HasRulesContract
{
    /**
     * バリデーションルール.
     * バリデーションを通るルートから入力される想定のあるカラム全てについてのバリデーションルールを記述.
     *
     * @return array
     */
    public static function rules(): array;

    /**
     * レコード作成時にのみ適用するルールを持つカラム名を定義する
     * @return array
     */
    public static function createOnlyRuleColumns(): array;

    /**
     * ルールの自然言語名定義
     * @return array
     */
    public static function ruleAttributes(): array;
}
