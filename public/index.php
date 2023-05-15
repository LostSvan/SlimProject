<?php

use Blog\Route\AboutPage;
use Blog\Route\BlogPage;
use Blog\Route\HomePage;
use Blog\Route\PostPage;
use Blog\slim\TwigMiddleware;
use DevCoder\DotEnv;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
require __DIR__ . '/../vendor/autoload.php';

$config = include '../config/database.php';

//$loader = new FilesystemLoader('../templates');
//$view = new Environment($loader);
$builder = new ContainerBuilder();
$builder->addDefinitions('../config/di.php');
$container = $builder->build();

(new DotEnv(dirname(__DIR__) . '/.env'))->load();

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->add($container->get(TwigMiddleware::class));
$app->get('/', HomePage::class . ':execute');
$app->get('/about', AboutPage::class);
$app->get('/blog[/{page}]', BlogPage::class);
$app->get('/post/{url_key}', PostPage::class);

$app->run();
