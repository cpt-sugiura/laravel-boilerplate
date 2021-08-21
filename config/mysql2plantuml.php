<?php

return [
    'dist_dir'        => 'docs/ER',
    'connection'      => [
        'driver'         => 'mysql',
        'url'            => env('DATABASE_URL'),
        'host'           => env('DB_HOST', '127.0.0.1'),
        'port'           => env('DB_PORT', '3306'),
        'database'       => env('DB_DATABASE_INFORMATION_SCHEMA', 'information_schema'),
        'username'       => env('DB_USERNAME', 'forge'),
        'password'       => env('DB_PASSWORD', ''),
        'unix_socket'    => env('DB_SOCKET', ''),
        'charset'        => 'utf8mb4',
        'collation'      => 'utf8mb4_unicode_ci',
        'prefix'         => '',
        'prefix_indexes' => true,
        'strict'         => true,
        'engine'         => null,
        'options'        => extension_loaded('pdo_mysql') ? array_filter(
            [
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]
        ) : [],
    ],
    'target_database' => env('DB_DATABASE'),
//    'relation_type'   => Relation::FORMAT_NUM,
    'relations'       => [
//        [
//            'from' => 'hoge',
//            'to' => 'fuga',
//        ],
//        [
//            'from' => 'foo',
//            'to' => 'bar',
//            'relation' => Relation::ONE_MANDATORY_TO_ONE_MANDATORY,
//            'direction' => Relation::DIRECTION_UP,
//            'arrowLength' => 4,
//        ],
    ],
    'packages' => [
        'システム管理' => [
            'migrations',
        ],
        '会員' => [
            'member_api_tokens',
            'members',
            'member_device_tokens',
            'member_password_reset_tokens',
        ],
        '管理者' => [
            'admins',
            'admin_password_reset_tokens',
        ],
    ],
    'free_comment' => '',
];
