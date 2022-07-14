<?php

/*
 * This file is part of vaibhavpandeyvpz/phemail package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace App\Mail\EmlFileLogger\Parser\Message;

/**
 * Class Header
 * @package App\Mail\Logger\Parser\Message
 */
class Header implements HeaderInterface
{
    /**
     * @var string
     */
    protected string $value;

    /**
     * @var string[]
     */
    protected array $attributes = [];

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param  string $value
     * @return static
     */
    public function withValue(string $value): Header|static
    {
        $clone        = clone $this;
        $clone->value = $value;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param  string $name
     * @param  string $value
     * @return static
     */
    public function withAttribute(string $name, string $value): Header|static
    {
        $clone                    = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $name): string
    {
        return $this->attributes[$name] ?? '';
    }
}
