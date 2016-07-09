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



// handle passwords, encryption and decryption
$container['Hashing'] = function($c) {
    $security = $c->get('settings')['security'];
    class Hashing {
        private $salt;
        private $cipherSeed;
        public function __construct($salt, $cipherSeed) {
            $this->salt = $salt;
            $this->cipherSeed = $cipherSeed;
        }
        public function password($string) {
            return password_hash($string, PASSWORD_BCRYPT, ['cost' => 12, 'salt' => $this->salt]);
        }
    }
    return new Hashing($security['salt'], $security['cipherSeed']);
};
