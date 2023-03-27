<?php

require dirname(__DIR__).'/vendor/autoload.php';

// For example:
use Slimfony\HttpFoundation\Request;
use Slimfony\Config\ConfigLoader;
use Slimfony\Routing\RouteResolver;


$request = Request::createFromGlobals();

$config = new ConfigLoader(dirname(__DIR__));
$rr = new RouteResolver($config);

var_dump($rr->resolveRoute($request, $rr->resolveRoutes()));
var_dump('<br>');
var_dump('<br>');

//(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
