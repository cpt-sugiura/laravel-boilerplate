<?php

namespace Tests\ApiToSwaggerYamlPresenter;

interface ApiToSwaggerAbleTest
{
    /**
     * @return string URL
     */
    public function route(): string;

    /**
     * @return string 自然言語API名称
     */
    public function label(): string;

    /**
     * @return string swagger 上のグループ化
     */
    public function tag(): string;

    /**
     * Swagger に書き込む際に使うテスト
     */
    public function testSuccessWithSwagger(): void;
}
