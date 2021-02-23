<?php

namespace App\Models;

use JsonException;

/**
 * Eloquent同様のプロパティ機能をつけるためのTrait.
 * プロパティ実体である$attributes配列の中身がプロパティ的に動作する.
 * $attributesの中身がtoArray, JSON化の対象になる.
 * implements Arrayable, \JsonSerializable, Jsonable
 * trait AttributesProperty
 * @package App\Models
 */
trait AttributesProperty
{
    protected array $attributes = [];

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Convert the fluent instance to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the fluent instance to JSON.
     *
     * @param  int           $options
     * @throws JsonException
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR | $options);
    }
}
