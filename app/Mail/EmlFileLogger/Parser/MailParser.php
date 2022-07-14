<?php

/*
 * This file is part of vaibhavpandeyvpz/phemail package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace App\Mail\EmlFileLogger\Parser;

use App\Mail\EmlFileLogger\Parser\Message\Header;
use App\Mail\EmlFileLogger\Parser\Message\MessagePart;

/**
 * Class MessageParser
 * @package App\Mail\Logger\Parser
 */
class MailParser implements MessageParserInterface
{
    public const REGEX_HEADER_LINE = '~^(?![\s]+)(?<name>[^:]+):\s+(?<content>(?<value>[^;]+).*)$~';

    public const REGEX_HEADER_LINE_EXTENDED = '~^\s+(?<content>.*)$~';

    public const REGEX_ATTRIBUTE = '~[;\s]+(?<name>[^=]+)=(?:["])?(?<value>[^;"]+)(?:["])?~';

    /**
     * {@inheritdoc}
     */
    public function parse($payload): MessagePart|Message\MessagePartInterface
    {
        if (is_string($payload)) {
            $iterator = new \ArrayIterator(file($payload, FILE_IGNORE_NEW_LINES));
        } elseif (is_array($payload)) {
            $iterator = new \ArrayIterator($payload);
        } elseif ($payload instanceof \Iterator) {
            $iterator = $payload;
        } else {
            throw new \InvalidArgumentException('$payload must be either string, array or an instance of \\Iterator');
        }
        $message = $this->parseHeaders($iterator, new MessagePart());
        $message = $this->parseMessage($iterator, $message);

        $message = $this->decodeHeaders($message);

        return $message->withContents($message->decoded()->getContents());
    }

    /**
     * @param  \Iterator   $lines
     * @param  MessagePart $part
     * @return MessagePart
     */
    protected function parseHeaders(\Iterator $lines, MessagePart $part): MessagePart
    {
        while ($lines->valid()) {
            $line = $lines->current();
            if (empty($line)) {
                break;
            }
            if (preg_match(self::REGEX_HEADER_LINE, $line, $matches)) {
                while ($lines->valid()) {
                    $lines->next();
                    $line = $lines->current();
                    if (preg_match(self::REGEX_HEADER_LINE_EXTENDED, $line, $matches2)) {
                        $matches['content'] .= ' '.trim($matches2['content']);
                        continue;
                    }
                    break;
                }
                $matches['name'] = strtolower($matches['name']);
                $header          = new Header();

                switch ($matches['name']) {
                    case 'content-disposition':
                    case 'content-type':
                        $header = $header->withValue($matches['value']);
                        if (preg_match_all(self::REGEX_ATTRIBUTE, $matches['content'], $attributes)) {
                            foreach ($attributes['name'] as $i => $attribute) {
                                $header = $header->withAttribute($attribute, $attributes['value'][$i]);
                            }
                        }
                        break;
                    default:
                        $header = $header->withValue($matches['content']);
                        break;
                }
                $part = $part->withHeader($matches['name'], $header);
            } else {
                $lines->next();
            }
        }

        return $part;
    }

    /**
     * @param  \Iterator   $lines
     * @param  MessagePart $part
     * @param  null        $boundary
     * @return MessagePart
     */
    protected function parseMessage(\Iterator $lines, MessagePart $part, $boundary = null): MessagePart
    {
        if ($part->isMultiPart()) {
            $boundary = $part->getHeaderAttribute('content-type', 'boundary');
            while ($lines->valid()) {
                $line = trim($lines->current());
                $lines->next();
                if ($line === "--$boundary") {
                    $sub  = $this->parseHeaders($lines, $sub = new MessagePart());
                    $sub  = $this->parseMessage($lines, $sub, $boundary);
                    $part = $part->withPart($sub);
                } elseif ($line === "--$boundary--") {
                    break;
                }
            }

            return $part;
        }

        if ($part->isMessage()) {
            $lines->next();
            $sub = $this->parseHeaders($lines, $sub = new MessagePart());
            $sub = $this->parseMessage($lines, $sub, $boundary);

            return $part->withPart($sub);
        }

        return $part->withContents($part->withContents($this->parseContent($lines, $boundary))->decoded()->getContents());
    }

    /**
     * @param  \Iterator $lines
     * @param            $boundary
     * @return string
     */
    protected function parseContent(\Iterator $lines, $boundary): string
    {
        $contents = [];
        while ($lines->valid()) {
            $line    = $lines->current();
            $trimmed = trim($line);
            if (is_null($boundary) || ($trimmed !== "--$boundary" && $trimmed !== "--$boundary--")) {
                $contents[] = $line;
            } else {
                break;
            }
            $lines->next();
        }

        return implode(PHP_EOL, $contents);
    }

    /**
     * @param  MessagePart $message
     * @return MessagePart
     */
    protected function decodeHeaders(MessagePart $message): MessagePart
    {
        $charset = $message->getHeaderAttribute('content-type', 'charset');
        if (! $charset && ($parts = $message->getParts()) && isset($parts[0])) {
            $charset = $parts[0]->getHeaderAttribute('content-type', 'charset');
        }

        $charset4Decode = match ($charset) {
            'utf-8', 'UTF-8' => 'utf-8',
            'iso-2022-jp' => 'ISO-2022-JP',
            default       => 'ascii'
        };
        foreach ($message->getHeaders() as $key => $header) {
            $headerStr = mb_convert_encoding(preg_replace_callback(
                '/=\?'.$charset4Decode.'\?([QB])\?(.*?)\?= ?/',
                static function ($matches) {
                    return $matches[1] === 'Q'
                        ? quoted_printable_decode($matches[2])
                        : base64_decode($matches[2]);
                },
                $header->getValue()
            ), 'utf-8', $charset4Decode);
            $message = $message->withHeader($key, $header->withValue($headerStr));
        }

        return $message;
    }
}
