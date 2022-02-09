<?php

use App\Library\Logging\DetailFormatter;
use App\Library\Logging\QueryLogFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

try {
    $__current_process_user = trim(shell_exec('whoami'));
} catch (ErrorException $e) {
    $__current_process_user = '';
}

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver'     => 'stack',
            'path'       => storage_path('logs/laravel-'.$__current_process_user.'.log'),
            'channels'   => ['daily'],
            'tap'        => [DetailFormatter::class],
            'days'       => 30,
            'permission' => 0664,
        ],

        'query_log' => [
            'driver'     => 'daily',
            'path'       => storage_path('logs/query-'.$__current_process_user.'.log'),
            'channels'   => ['daily'],
            'tap'        => [QueryLogFormatter::class],
            'days'       => 30,
            'permission' => 0664,
        ],

        'single' => [
            'driver'     => 'single',
            'path'       => storage_path('logs/laravel-'.$__current_process_user.'.log'),
            'level'      => 'debug',
            'permission' => 0664,
        ],

        'daily' => [
            'driver'     => 'daily',
            'path'       => storage_path('logs/laravel-'.$__current_process_user.'.log'),
            'level'      => 'debug',
            'days'       => 30,
            'permission' => 0664,
        ],

        'slack' => [
            'driver'   => 'slack',
            'url'      => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji'    => ':boom:',
            'level'    => 'error', // ←ここの値を使いたいログのレベルの最低値にする。今回は例外以上の異常事態だけ飛ばしたいのでerrorレベル
        ],

        'develop_slack' => [
            'driver'   => 'slack',
            'url'      => env('DEVELOP_LOG_SLACK_WEBHOOK_URL'),
            'username' => env('APP_NAME'),
            'emoji'    => ':boom:',
            'level'    => 'debug',
        ],

        'papertrail' => [
            'driver'       => 'monolog',
            'level'        => env('LOG_LEVEL', 'debug'),
            'handler'      => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host'             => env('PAPERTRAIL_URL'),
                'port'             => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver'    => 'monolog',
            'level'     => env('LOG_LEVEL', 'debug'),
            'handler'   => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with'      => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path'       => storage_path('logs/laravel-'.$__current_process_user.'.log'),
            'permission' => 0664,
        ],
    ],
];
