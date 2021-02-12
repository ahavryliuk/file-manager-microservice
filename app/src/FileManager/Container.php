<?php

namespace FileManager;

use DI\ContainerBuilder;
use Noodlehaus\ConfigInterface;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private \DI\Container $container;

    public function __construct()
    {
        $containerBuilder = new ContainerBuilder();

        $rootDir = dirname(__DIR__, 2);

        $containerBuilder->addDefinitions([
            \Psr\Http\Message\RequestFactoryInterface::class => \DI\autowire(\Slim\Psr7\Factory\RequestFactory::class),
            \Psr\Http\Message\ResponseFactoryInterface::class => \DI\autowire(\Slim\Psr7\Factory\ResponseFactory::class),

            ConfigInterface::class => \DI\autowire(\Noodlehaus\Config::class),
            \Noodlehaus\Config::class => function () use ($rootDir) {
                return new \Noodlehaus\Config($rootDir . '/config/config.default.php');
            },

            \Slim\App::class => function (ContainerInterface $container) {
                $app = \Slim\Factory\AppFactory::create($container->get(\Slim\Psr7\Factory\ResponseFactory::class), $container);
                $app->addBodyParsingMiddleware();
                $app->addRoutingMiddleware();

                return $app;
            },

            \Doctrine\DBAL\Connection::class => function (ContainerInterface $container) {
                $config = $container->get(\Noodlehaus\Config::class);
                $configuration = new \Doctrine\DBAL\Configuration();

                return \Doctrine\DBAL\DriverManager::getConnection(
                    $config->get('database'),
                    $configuration
                );
            },
        ]);

        $this->container = $containerBuilder->build();
    }

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function has($id)
    {
        return $this->container->has($id);
    }

    public function set($id, $value)
    {
        $this->container->set($id, $value);
    }
}