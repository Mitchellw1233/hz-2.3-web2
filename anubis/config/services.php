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
    // Global
    \Slimfony\Config\ConfigLoader::class => [
        'args' => [$projectDir],
    ],

    // ORM
    \Slimfony\ORM\Resolver\MappingResolver::class => [],
    \Slimfony\ORM\Driver::class => [
        'shared' => true,
        // ConfigLoader
    ],
    \Slimfony\ORM\EntityTransformer::class => [
        // MappingResolver, Driver
    ],
    \Slimfony\ORM\EntityManager::class => [
        // Driver, MappingResolver, EntityTransformer
    ],

    // Framework
    \Slimfony\HttpKernel\ControllerResolver::class => [
        // Container
    ],
    \Slimfony\Routing\RouteResolver::class => [
        // ConfigLoader
    ],
    \Slimfony\HttpKernel\EventListener\TypeResponseListener::class => [],
    \Slimfony\HttpKernel\EventListener\RouterListener::class => [
        // RouteResolver
    ],
    \Slimfony\EventDispatcher\EventDispatcher::class => [
        'methods' => [
            ['method' => 'addSubscriber', 'args' => [
                new Reference(\Slimfony\HttpKernel\EventListener\RouterListener::class),
            ]],
            ['method' => 'addSubscriber', 'args' => [
                new Reference(\Slimfony\HttpKernel\EventListener\TypeResponseListener::class),
            ]],
        ],
    ],
    \Slimfony\Templating\Template::class => [
        'args' => [$projectDir.'/templates']
    ],
];

/**
 * @var array<string, array{
 *     args?: array<int, mixed|Reference>,
 *     methods?: array<int, array{method: string, args?: array<int, mixed|Reference>}>
 * }> $anubis
 */
$anubis = [
    \App\Controller\BlogApiController::class => [
        // Container
    ],
    \App\Controller\Admin\ExamController::class => [
        // Container
    ]
];

/**
 * @return array<string, array{
 *     args?: array<int, mixed|Reference>,
 *     methods?: array<int, array{method: string, args?: array<int, mixed|Reference>}>
 * }>
 */
return [...$slimfony, ...$anubis];
