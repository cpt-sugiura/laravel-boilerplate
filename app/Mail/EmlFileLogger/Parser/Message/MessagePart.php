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
 * Class MessagePart
 * @package App\Mail\Logger\Parser\Message
 */
class MessagePart implements MessagePartInterface
{
    /**
     * @var HeaderInterface[]
     */
    protected array $headers = [];

    /**
     * @var string
     */
    protected string $contents = '';

    /**
     * @var MessagePartInterface[]
     */
    protected array $attachments = [];

    /**
     * @var MessagePartInterface[]
     */
    protected array $parts = [];

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(string $name): ?HeaderInterface
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderAttribute(string $header, string $attr, string $default = null): ?string
    {
        $headerInstance = $this->getHeader($header);
        if ($headerInstance && ($attribute = $headerInstance->getAttribute($attr))) {
            return $attribute;
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderValue(string $name, string $default = null): ?string
    {
        $header = $this->getHeader($name);

        return $header ? $header->getValue() : $default;
    }

    public function withHeader($name, HeaderInterface $header): MessagePart
    {
        $clone                 = clone $this;
        $clone->headers[$name] = $header;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType(): string
    {
        return $this->getHeaderValue('content-type', 'text/plain');
    }

    /**
     * {@inheritdoc}
     */
    public function isMultiPart(): bool
    {
        return stripos($this->getContentType(), 'multipart/') === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isMessage(): bool
    {
        return stripos($this->getContentType(), 'message/') === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isText(): bool
    {
        return stripos($this->getContentType(), 'text/') === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * @param  string $contents
     * @return static
     */
    public function withContents(string $contents): MessagePart | static
    {
        $clone           = clone $this;
        $clone->contents = $contents;

        return $clone;
    }

    public function decoded(): self
    {
        if ($this->getHeaderValue('content-transfer-encoding') === 'quoted-printable') {
            $this->contents = quoted_printable_decode($this->contents);
        }
        if ($this->getHeaderAttribute('content-type', 'charset')) {
            $this->contents = mb_convert_encoding(
                $this->contents,
                'utf-8',
                $this->getHeaderAttribute('content-type', 'charset')
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParts($recursive=false): array
    {
        $ret = $this->parts;
        if ($recursive) {
            $parts = [];
            foreach ($this->parts as $part) {
                $parts[] = $part->getParts(true);
            }
            $ret = array_merge($ret, ...$parts);
        }

        return $ret;
    }

    /**
     * @param  MessagePartInterface $part
     * @return static
     */
    public function withPart(MessagePartInterface $part): MessagePart | static
    {
        $clone = clone $this;
        if ($part->getHeaderValue('content-disposition') === 'attachment') {
            $clone->attachments[] = $part;
        } else {
            $clone->parts[] = $part;
        }

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachments($recursive=false): array
    {
        $ret = $this->attachments;
        if ($recursive) {
            $partsAttachments = [];
            foreach ($this->parts as $part) {
                $partsAttachments[] = $part->getAttachments(true);
            }
            $ret = array_merge($ret, ...$partsAttachments);
        }

        return $ret;
    }
}
