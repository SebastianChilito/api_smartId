<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// manager mongodb
$container['conexiondb'] = function ($c) {
    $settingsDb = $c->get('settings')['database'];
    $client = new MongoDB\Client("mongodb://" . $settingsDb['host'] . ":" . $settingsDb['port'], ['readPreference' => 'secondaryPreferred']
            //,['typeMap' => ['root' => 'array', 'document' => 'array']]
    );
    return $client->selectDatabase($settingsDb['database']);
};