<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
        
        //database mongodb
        'database'=>[            
            'host' => '127.0.0.1',
            'port' => '27017',
            'database' => 'samlook',
//            'username' => 'root',
//            'password' => '123456',
        ]
    ],
];
