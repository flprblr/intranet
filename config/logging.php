<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

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

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

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
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'sales-daily' => [
            'driver' => 'single',
            'path' => storage_path('logs/sales/daily.log'),
            'level' => 'info',
        ],

        'sales-monthly' => [
            'driver' => 'single',
            'path' => storage_path('logs/sales/monthly.log'),
            'level' => 'info',
        ],

        'woo-orders' => [
            'driver' => 'single',
            'path' => storage_path('logs/woocommerce/orders.log'),
            'level' => 'info',
        ],

        'woo-stocks' => [
            'driver' => 'single',
            'path' => storage_path('logs/woocommerce/stock.log'),
            'level' => 'info',
        ],

        'sovos' => [
            'driver' => 'single',
            'path' => storage_path('logs/sovos/orders.log'),
            'level' => 'info',
        ],

        'pos' => [
            'driver' => 'single',
            'path' => storage_path('logs/pos/orders.log'),
            'level' => 'info',
        ],

        'mv-sovos' => [
            'driver' => 'single',
            'path' => storage_path('logs/sovos/mv/orders.log'),
            'level' => 'info',
        ],

        'mv-pos' => [
            'driver' => 'single',
            'path' => storage_path('logs/pos/mv/orders.log'),
            'level' => 'info',
        ],
        'mv-sap' => [
            'driver' => 'single',
            'path' => storage_path('logs/sap/mv/orders.log'),
            'level' => 'info',
        ],

        'mv-multivende' => [
            'driver' => 'single',
            'path' => storage_path('logs/multivende/mv/orders.log'),
            'level' => 'info',
        ],

        'adidas' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/adidas.log'),
            'level' => 'info',
        ],

        'apple' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/apple.log'),
            'level' => 'info',
        ],

        'converse' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/converse.log'),
            'level' => 'info',
        ],

        'followup' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/followup.log'),
            'level' => 'info',
        ],

        'gfk' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/gfk.log'),
            'level' => 'info',
        ],

        'nike' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/nike.log'),
            'level' => 'info',
        ],

        'puma' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/puma.log'),
            'level' => 'info',
        ],

        'reebok' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/reebok.log'),
            'level' => 'info',
        ],

        'shoppertrack' => [
            'driver' => 'single',
            'path' => storage_path('logs/ftp/shoppertrack.log'),
            'level' => 'info',
        ],

    ],

];
