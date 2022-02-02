<?php

namespace App\Library\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\ProcessableHandlerInterface;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;

class QueryLogFormatter
{
    private const LOG_FORMAT = '[%datetime% %channel%.%level_name% url:%extra.url% ip:%extra.ip% uid:%extra.uid%] %message% %context%'."\n";

    /**
     * 渡されたロガーインスタンス（Monolog インスタンス）のカスタマイズ
     *
     * @param  Logger  $monolog
     * @return void
     */
    public function __invoke(Logger $monolog)
    {
        $formatter = new LineFormatter(self::LOG_FORMAT, 'Y/m/d H:i:s.v', true, true);

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
