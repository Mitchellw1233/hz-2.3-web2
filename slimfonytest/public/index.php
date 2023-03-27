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

//var_dump(new Route('test', 'test', 'test ', ['test']));
//var_dump($config->getRoutes());


//$test = preg_replace_callback('#.+(?=\?|\#)+#', fn($matches) => $matches[0], $test);
//var_dump($test);

//(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
