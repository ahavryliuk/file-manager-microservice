<?php

use FileManager\Container;
use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$app = $container->get(App::class);

$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

$app->run();
