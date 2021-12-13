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
 * Interface HeaderInterface
 * @package App\Mail\Logger\Parser\Message
 */
interface HeaderInterface
{
    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return string[]
     */
    public function getAttributes(): array;

    /**
     * @param  string $name
     * @return string
     */
    public function getAttribute(string $name): string;
}
