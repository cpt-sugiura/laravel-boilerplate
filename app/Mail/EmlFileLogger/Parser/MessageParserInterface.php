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

use App\Mail\EmlFileLogger\Parser\Message\MessagePartInterface;

/**
 * Interface MessageParserInterface
 * @package App\Mail\Logger\Parser
 */
interface MessageParserInterface
{
    /**
     * @param  string|array|\Iterator $payload
     * @return MessagePartInterface
     */
    public function parse($payload);
}
