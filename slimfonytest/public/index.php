<?php

require dirname(__DIR__).'/vendor/autoload.php';

use App\Kernel;
use Slimfony\EventDispatcher\EventDispatcher;
use Slimfony\HttpFoundation\Request;
use Slimfony\Config\ConfigLoader;
use Slimfony\HttpKernel\EventListener\RouterListener;
use Slimfony\HttpKernel\EventListener\TypeResponseListener;
use Slimfony\Routing\RouteResolver;
use Slimfony\HttpKernel\ControllerResolver;


$config = new ConfigLoader(dirname(__DIR__));
$cr = new ControllerResolver();
$rr = new RouteResolver($config);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($rr));
$dispatcher->addSubscriber(new TypeResponseListener());

$kernel = new Kernel($dispatcher, $cr);

$response = $kernel->handle(Request::createFromGlobals());
$response->send();
