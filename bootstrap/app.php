<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../App/common.php';

$config = require __DIR__ . '/../bootstrap/config.php';
$app = new \Slim\App($config);


$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

$container['db'] = function ($c) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($c['settings']['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

//mono logger
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], $settings['logger']['level']));
    return $logger;
};


/*****************************************
 * @param $container
 * @return \App\Controllers\HomeController
 * defined controllers here!
 *
 */

$container['TestsController'] = function( $container ){
    return new \App\Controllers\TestsController($container);
};

$container['HomeController'] = function( $container ){
    return new \App\Controllers\HomeController($container);
};

$container['AirqualityController'] = function( $container ){
    return new \App\Controllers\AirqualityController($container);
};

$container['EmaController'] = function( $container ){
    return new \App\Controllers\EmaController($container);
};


require __DIR__ . '/../App/middleware.php';
require __DIR__ . '/../App/routes.php';
