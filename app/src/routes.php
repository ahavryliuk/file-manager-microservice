<?php

use FileManager\Rest\Controller\FileController;
use FileManager\Rest\Middleware\ErrorMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;

return function (App $app) {
    $app->add(ErrorMiddleware::class);

    $app->group('/files', function (RouteCollectorProxyInterface $group) {
        $group->get('/info', FileController::class . ':info');

        $group->get('/{external_id}', FileController::class . ':get');
        $group->delete('/{external_id}/{token}', FileController::class . ':delete');

        $group->post('', FileController::class . ':upload');

        $group->get('', FileController::class . ':list');
    });
};