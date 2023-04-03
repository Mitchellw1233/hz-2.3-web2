<?php

use App\Kernel;
use Slimfony\DependencyInjection\Reference;

$projectDir = Kernel::getProjectDir();

/**
 * @var array<string, array{
 *     args?: array<int, mixed|Reference>,
 *     methods?: array<int, array{method: string, args?: array<int, mixed|Reference>}>
 * }> $slimfony
 */
$slimfony = [
    'Slimfony\Config\ConfigLoader' => [
        'args' => [$projectDir],
    ],
    'Slimfony\HttpKernel\ControllerResolver' => [
//        'args' => [new Reference(\Slimfony\DependencyInjection\Container::class)],
    ],
    'Slimfony\Routing\RouteResolver' => [
//        'args' => [new Reference(\Slimfony\Config\ConfigLoader::class)]
    ],
    'Slimfony\HttpKernel\EventListener\TypeResponseListener' => [],
    'Slimfony\HttpKernel\EventListener\RouterListener' => [
//        'args' => [new Reference(\Slimfony\Routing\RouteResolver::class)]
    ],
    'Slimfony\EventDispatcher\EventDispatcher' => [
        'methods' => [
            ['method' => 'addSubscriber', 'args' => [
                new Reference(\Slimfony\HttpKernel\EventListener\RouterListener::class),
            ]],
            ['method' => 'addSubscriber', 'args' => [
                new Reference(\Slimfony\HttpKernel\EventListener\TypeResponseListener::class),
            ]],
        ],
    ],

    'Slimfony\Templating\Template' => [
        'args' => [$projectDir.'/templates']
    ],
];

/**
 * @var array<string, array{
 *     args?: array<int, mixed|Reference>,
 *     methods?: array<int, array{method: string, args?: array<int, mixed|Reference>}>
 * }> $slimfonytest
 */
$slimfonytest = [
    'App\Controller\BlogApiController' => [
//        'args' => [new Reference(\Slimfony\DependencyInjection\Container::class)],
    ],
];

/**
 * @return array<string, array{
 *     args?: array<int, mixed|Reference>,
 *     methods?: array<int, array{method: string, args?: array<int, mixed|Reference>}>
 * }>
 */
return [...$slimfony, ...$slimfonytest];
