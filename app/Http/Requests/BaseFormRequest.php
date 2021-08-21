<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

abstract class BaseFormRequest extends FormRequest
{
    abstract public function rules(): array;

    /**
     * リクエストの中からバリデーションされたパラメータをプロパティ的に取得できるようにする
     *
     * @param  string $key
     * @return mixed
     * @noinspection MagicMethodsValidityInspection
     */
    public function __get($key)
    {
        // リクエスト内容は読み取り専用なので__setは不要
        return isset($this->validator) ? Arr::get(
            $this->validated(),
            $key,
            function () use ($key) {
                return $this->route($key);
            }
        ) : parent::__get($key);
    }

    /**
     * @return Validator
     */
    protected function getValidatorInstance()
    {
        $validator =  parent::getValidatorInstance();
        $validator->setValueNames($this->values());
        $this->setValidator($validator);

        return $this->validator;
    }

    /**
     * :value の置き換え
     * @see https://readouble.com/laravel/6.x/ja/validation.html?header=%E8%A8%80%E8%AA%9E%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB%E3%81%A7%E3%82%AB%E3%82%B9%E3%82%BF%E3%83%A0%E5%80%A4%E3%82%92%E6%8C%87%E5%AE%9A
     * @return array
     */
    protected function values(): array
    {
        return [];
    }

    /**
     * スネークケースを付与
     * @return array<string>
     */
    public function validated()
    {
        $validated = parent::validated();

        return array_merge($validated, array_key_snake($validated));
    }

    /**
     * @return array<string>
     */
    public function getRuleDescriptionList(): array
    {
        $returnStringList = [];

        foreach ($this->rules() as $name => $rule) {
            if (is_string($rule)) {
                $returnStringList[$name] = implode(': ', [$this->getAttribute($name), $rule]);
                continue;
            }

            $returnStringList[$name] = implode(': ', [
                $this->getAttribute($name),
                collect($rule)->map(static function ($r) {
                    if (is_string($r)) {
                        return $r;
                    }

                    if (method_exists($r, '__toString')) {
                        return $r->__toString();
                    }

                    return class_basename($r);
                })->join(', ')
            ]);
        }

        return $returnStringList;
    }

    protected function getAttribute(string $key)
    {
        return $this->attributes()[$key] ?? $key;
    }

    public function getRuleDescription(string $key): string
    {
        return $this->getRuleDescriptionList()[$key] ?? '';
    }
}
