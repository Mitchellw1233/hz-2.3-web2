<?php

require dirname(__DIR__).'/vendor/autoload.php';

// For example:
use Slimfony\HttpFoundation\Request;
use Slimfony\Config\ConfigLoader;
use Slimfony\Routing\RouteResolver;
use Slimfony\HttpKernel\ControllerResolver;

$request = Request::createFromGlobals();

$config = new ConfigLoader(dirname(__DIR__));
$cr = new ControllerResolver();
$rr = new RouteResolver($config);

$route = $rr->resolveRoute($request, $rr->resolveRoutes());

$controller = $cr->getController($route);
$args = $cr->getArguments($route, $controller);

call_user_func_array($controller, $args);

//var_dump($rr->resolveRoute($request, $rr->resolveRoutes()));
//var_dump('<br>');
//var_dump('<br>');


//(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
