<?php

require dirname(__DIR__).'/vendor/autoload.php';

// For example:
use Slimfony\Routing\Route;
use Slimfony\Config\ConfigLoader;

$config = new ConfigLoader();

var_dump(new Route('test', 'test', 'test ', ['test']));
var_dump($config->getRoutes());

//require dirname(__DIR__).'/vendor/autoload.php';

//(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
