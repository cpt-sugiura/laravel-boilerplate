<?php

namespace App\Models\Search\BaseQueryBuilder;

use App\Http\Presenters\BasePresenter;

interface HasFormatterContract
{
    /**
     * 検索結果を読みやすい構造体に変換する
     * @param  object              $item
     * @return array|BasePresenter
     */
    public static function resultFormatter(object $item);
}
