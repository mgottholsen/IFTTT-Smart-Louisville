<?php
return [
    'settings' => [
        'env' => 'test', //test / prod
        'displayErrorDetails' => false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        'system' => [
            'domain' => '<YOUR_DOMAIN_HERE>',
            'max_file_size' => (10 * 1024 * 1024) //5mb
        ],
        // Renderer settings
        'view' => [
            'template_path' => __DIR__ . '/../resources/views',
            'cache_path' => __DIR__ . '/../resources/tmp',
            'debug' => true,
            'cache' => false
        ],
        // Monolog settings
        'logger' => [
            'name' => '<YOUR_NAME_HERE>',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        //Database Info
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'IFTTT_DATABASE_NAME',
            'username' => 'USERNAME',
            'password' => 'PASSWORD',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'sendgrid' => [
            'api_key' => 'SEND_GRID_API_KEY'
        ],
        'twilio' => [
            'test' => [
                'keysid' => 'KEYSID',
                'keysecret' => 'KEYSECRET',
                'accountsid' => 'ACCOUNTSID',
                'authtoken' => 'AUTHTOKEN',
                'number' => 'NUMBER'
            ],
            'live' => [
                'keysid' => 'KEYSID',
                'keysecret' => 'KEYSECRET',
                'accountsid' => 'ACCOUNTSID',
                'authtoken' => 'AUTHTOKEN',
                'number' => 'NUMBER'
            ]
        ],
    ],
];
