<?php

require dirname(__DIR__).'/vendor/autoload.php';

// For example:
use Slimfony\HttpFoundation\Request;
use Slimfony\Routing\Route;
use Slimfony\Config\ConfigLoader;
use Slimfony\Routing\RouteCollection;
use Slimfony\Routing\RouteResolver;


$request = Request::createFromGlobals();

$config = new ConfigLoader(dirname(__DIR__));
$rr = new RouteResolver($config);

var_dump($rr->resolveRoute($request, new RouteCollection()));

$route1 = new Route('test', 'test', 'test ', ['test']);
$route2 = new Route('foo', 'test', 'test ', ['test']);

$routeCollection = new RouteCollection();

$routeCollection->add($route1);
$routeCollection->add($route2);

$routeCollection->remove($route2);

var_dump($routeCollection);

//var_dump(new Route('test', 'test', 'test ', ['test']));
var_dump($config->getRoutes());

//(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
