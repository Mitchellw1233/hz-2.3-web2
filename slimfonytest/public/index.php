<?php

require dirname(__DIR__).'/vendor/autoload.php';

// For example:
use Slimfony\Routing\Route;
use Slimfony\Config\ConfigLoader;
use Slimfony\Routing\RouteCollection;

$config = new ConfigLoader(dirname(__DIR__));

$route1 = new Route('test', 'test', 'test ', ['test']);
$route2 = new Route('foo', 'test', 'test ', ['test']);

$routeCollection = new RouteCollection();

$routeCollection->add($route1);
$routeCollection->add($route2);

$routeCollection->remove($route2);

var_dump($routeCollection);

//var_dump(new Route('test', 'test', 'test ', ['test']));
//var_dump($config->getRoutes());

//(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
