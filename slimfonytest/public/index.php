<?php

require dirname(__DIR__).'/vendor/autoload.php';

use App\Kernel;
use Slimfony\DependencyInjection\ContainerBuilder;
use Slimfony\DependencyInjection\Reference;
use Slimfony\HttpFoundation\Request;

/**
 * @var array<string, array{
 *     args?: array<int, mixed|Reference>,
 *     methods?: array<int, array{method: string, args?: array<int, mixed|Reference>}>
 * }> $services
 */
$services = include dirname(__DIR__) . '/config/services.php';

$cb = new ContainerBuilder();
$cb->registerAll($services);

$kernel = new Kernel($cb->getContainer());

$response = $kernel->handle(Request::createFromGlobals());
$response->send();
