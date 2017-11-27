<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../src/common.php';

$env = 'prod'; // Options are dev,tst,prod

switch ($env) {
    case "dev":
        $config = require __DIR__ . '/../../dev.config.php';
        break;
    case "tst":
        $config = require __DIR__ . '/../../tst.config.php';
        break;
    case "prod":
        $config = require __DIR__ . '/../../prod.config.php';
        break;
    default:
        $config = require __DIR__ . '/../../prod.config.php';
        break;
}

$app = new \Slim\App($config);


$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../templates/', [
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
 * @return \Src\Controllers\HomeController
 * defined controllers here!
 *
 */

$container['TestsController'] = function( $container ){
    return new \Src\Controllers\TestsController($container);
};

$container['HomeController'] = function( $container ){
    return new \Src\Controllers\HomeController($container);
};

$container['AirqualityController'] = function( $container ){
    return new \Src\Controllers\AirqualityController($container);
};

$container['EmaController'] = function( $container ){
    return new \Src\Controllers\EmaController($container);
};

$container['RoadAlertsController'] = function( $container ){
    return new \Src\Controllers\RoadAlertsController($container);
};

$container['RestaurantInspectionController'] = function( $container ){
    return new \Src\Controllers\RestaurantInspectionController($container);
};

require __DIR__ . '/../src/middleware.php';
require __DIR__ . '/../src/routes.php';
