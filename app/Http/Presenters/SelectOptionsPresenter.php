<?php

namespace App\Http\Presenters;

use Illuminate\Support\Collection;

/**
 * フロントエンドのselect要素の選択肢に使いやすい形で情報を送るためのプレゼンター。
 * {label: string; value: string|number }[] なレスポンスを返す為に使う。
 * Class SelectElementPresenter
 * @package App\Http\Presenters
 */
class SelectOptionsPresenter extends BasePresenter
{
    /** @var Collection */
    protected Collection $collection;
    /** @var string */
    protected string $labelKey;
    /** @var string */
    protected string $valueKey;

    /**
     * SelectElementPresenter constructor.
     * @param Collection $collection
     * @param string     $labelKey
     * @param string     $valueKey
     */
    public function __construct(Collection $collection, string $labelKey, string $valueKey)
    {
        $this->collection = $collection;
        $this->labelKey   = $labelKey;
        $this->valueKey   = $valueKey;
    }

    public function toArray(): array
    {
        $labelKey = $this->labelKey;
        $valueKey = $this->valueKey;

        return $this->collection->map(
            static function ($item) use ($valueKey, $labelKey) {
                return [
                    'label' => is_array($item) ? $item[$labelKey] : $item->$labelKey,
                    'value' => is_array($item) ? $item[$valueKey] : $item->$valueKey,
                ];
            }
        )->toArray();
    }

    /**
     * 一次元配列を元に選択肢に使いやすい感じにフォーマットするプレゼンタを返す.
     * @param  array|Collection       $arr
     * @return SelectOptionsPresenter
     */
    public static function createByOneDimensionalArr($arr): SelectOptionsPresenter
    {
        $newKey  = 0;
        $collect = collect($arr)->mapWithKeys(static function ($item, $originalKey) use (&$newKey) {
            return [$newKey++ => ['label' => $item, 'value' => $originalKey]];
        });

        return new SelectOptionsPresenter($collect, 'label', 'value');
    }
}
