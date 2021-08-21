<?php

namespace App\Library\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\ProcessableHandlerInterface;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;

class DetailFormatter
{
    /**
     * 渡されたロガーインスタンス（Monolog インスタンス）のカスタマイズ
     *
     * @param  Logger $monolog
     * @return void
     */
    public function __invoke($monolog)
    {
        $formatter = new JsonFormatter();

        $processors = [
            new IntrospectionProcessor(Logger::DEBUG, ['Illuminate\\']),
            new UidProcessor(),
            new WebProcessor(),
            new ProcessIdProcessor(),
        ];
        foreach ($monolog->getHandlers() as $handler) {
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter($formatter);
            }
            if ($handler instanceof ProcessableHandlerInterface) {
                foreach ($processors as $p) {
                    $handler->pushProcessor($p);
                }
            }
        }
    }
}
