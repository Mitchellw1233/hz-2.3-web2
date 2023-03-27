<?php

require dirname(__DIR__).'/vendor/autoload.php';

// For example:
use Slimfony\Routing\Route;
use Slimfony\Config\ConfigLoader;

$config = new ConfigLoader(dirname(__DIR__));

//var_dump(new Route('test', 'test', 'test ', ['test']));
var_dump($config->getRoutes());

//(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
