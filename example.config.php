<?php
return [
    'settings' => [
        'displayErrorDetails' => false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        'system' => [
            'domain' => 'http://yourdomain.com',
            'max_file_size' => (10 * 1024 * 1024) //5mb
        ],
        // Renderer settings
        'view' => [
            'template_path' => __DIR__ . '/DOC_ROOT/templates/views', // Replace DOC_ROOT with the project directory path.
            'cache_path' => __DIR__ . '/DOC_ROOT/templates/tmp', // Replace DOC_ROOT with the project directory path.
            'debug' => true,
            'cache' => false
        ],
        // Monolog settings
        'logger' => [
            'name' => 'yourdomain.com',
            'path' => __DIR__ . '/doc_root/logs/app.log', // Replace doc_root with the project directory path.
            'level' => \Monolog\Logger::DEBUG,
        ],
        //Database Info
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'DB_NAME', // Database Name
            'username' => 'DB_USER', // Database User
            'password' => 'DB_PASS', // Database User Password
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'sendgrid' => [
            'api_key' => 'SENDGRID_API_KEY' // Your SendGrid API Key
        ],
        'twilio' => [
            'test' => [
                'keysid' => 'YOUR_KEY_SID',
                'keysecret' => 'YOU_KEY_SECRET',
                'accountsid' => 'YOUR_ACCOUNT_SID',
                'authtoken' => 'YOUR_AUTH_TOKEN',
                'number' => 'YOUR_SENDGRID_NUMBER' // No dashes, no parenthesis(ex: 5021113456)
            ],
            'live' => [
                'keysid' => 'YOUR_KEY_SID',
                'keysecret' => 'YOU_KEY_SECRET',
                'accountsid' => 'YOUR_ACCOUNT_SID',
                'authtoken' => 'YOUR_AUTH_TOKEN',
                'number' => 'YOUR_SENDGRID_NUMBER' // No dashes, no parenthesis(ex: 5021113456)
            ]
        ],
        'ifttt_vault' => [
            'airQualityURL' => 'http://www.airnowapi.org/aq/observation/zipCode/current/?format=application/json&zipCode=ZIP_CODE&distance=25&API_KEY=YOUR_API_KEY', // Replace ZIP_CODE and YOUR_API_KEY with proper data.
            'getRave' => 'http://www.getrave.com/cap/YOUR_ID/YOUR_CHANNEL' // Replace YOUR_ID and YOUR_CHANNEL with proper data.
        ],
    ],
];