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
 * Interface MessagePartInterface
 * @package App\Mail\Logger\Parser\Message
 */
interface MessagePartInterface
{
    /**
     * @return HeaderInterface[]
     */
    public function getHeaders(): array;

    /**
     * @param  string               $name
     * @return HeaderInterface|null
     */
    public function getHeader(string $name): ?HeaderInterface;

    /**
     * @param  string      $header
     * @param  string      $attr
     * @param  string|null $default
     * @return string|null
     */
    public function getHeaderAttribute(string $header, string $attr, string $default = null): ?string;

    /**
     * @param  string      $name
     * @param  string|null $default
     * @return string|null
     */
    public function getHeaderValue(string $name, string $default = null): ?string;

    /**
     * @return bool
     */
    public function isMultiPart(): bool;

    /**
     * @return bool
     */
    public function isMessage(): bool;

    /**
     * @return bool
     */
    public function isText(): bool;

    /**
     * @return string
     */
    public function getContentType(): string;

    /**f
     * @return string
     */
    public function getContents(): string;

    /**
     * @return MessagePartInterface[]
     */
    public function getAttachments($recursive=false): array;

    /**
     * @return MessagePartInterface[]
     */
    public function getParts($recursive=false): array;
}
