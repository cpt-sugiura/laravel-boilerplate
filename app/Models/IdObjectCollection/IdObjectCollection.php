<?php

namespace App\Models\IdObjectCollection;

use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * xxx_id を持つオブジェクトの配列を元に色々やるクラスの基底
 * Class IdObjectCollection
 * @package App\Models\RelatinLoader
 */
abstract class IdObjectCollection
{
    /** @var Collection xxx_idプロパティを持つオブジェクトのコレクション */
    protected Collection $items;

    /**
     * CoinCollection constructor.
     * @param  Collection|object[]  $items  xxx_idプロパティを持つオブジェクトの配列かコレクション
     */
    public function __construct(array|Collection $items = [])
    {
        if (is_array($items)) {
            $items = collect($items);
        }
        if (! ($items instanceof Collection)) {
            throw new InvalidArgumentException(Collection::class.'か配列を引数に指定してください');
        }
        $this->items = $items;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }
}
