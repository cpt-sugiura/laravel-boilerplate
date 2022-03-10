<?php

namespace App\Http\Presenters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonException;
use JsonSerializable;

abstract class BasePresenter implements Arrayable, JsonSerializable, Jsonable
{
    /**
     * 配列に変換する. このオブジェクトをレスポンスとして返すと、この配列のJSON版がレスポンスになるのを想定
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * JSON にシリアライズしたいデータを指定する
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * JSONに変換する
     *
     * @param  int           $options
     * @throws JsonException
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR | $options, 512);
    }
}
