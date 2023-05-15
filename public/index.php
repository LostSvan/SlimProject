<?php

use Blog\Database;
use Blog\LatestPost;
use Blog\Route\HomePage;
use Blog\twig\AssetExtension;
use DevCoder\DotEnv;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Blog\Postmapper;

require __DIR__ . '/../vendor/autoload.php';
$config = include '../config/database.php';

//$loader = new FilesystemLoader('../templates');
//$view = new Environment($loader);
$builder = new ContainerBuilder();
$builder->addDefinitions('../config/di.php');
$container = $builder->build();

(new DotEnv(dirname(__DIR__) . '/.env'))->load();

AppFactory::setContainer($container);
$view = $container->get(Environment::class);
$connect = $container->get(Database::class)->getConnect();

$app = AppFactory::create();
$app->add(new \Blog\slim\TwigMiddleware($view));
$app->get('/', HomePage::class . ':execute');
$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig', ['name' => 'Vlad']);
    $response->getBody()->write($body);
    return $response;
});
$app->get('/blog[/{page}]', function (Request $request, Response $response, $args) use ($view, $connect) {
//    current это текущий
//    paging это коло-во страниц
    $listPost = new Postmapper($connect);
    $page = isset($args['page']) ? (int)$args['page'] : 1;
    $limit = 2;
    $totalCount = ($listPost->getCountList());
    $posts = $listPost->getList($page, $limit, 'DESC');
    $body = $view->render('blog.twig', [
        'posts' => $posts,
        'current' => $page,
        'paging' => ceil($totalCount / $limit),
        ]);
    $response->getBody()->write($body);
    return $response;
});
$app->get('/post/{url_key}', function (Request $request, Response $response, $args) use ($connect, $view) {
    $postObject = new Postmapper($connect);
    $post = $postObject->getByUrlKey($args['url_key']);
    if (empty($post)) {
        $body = $view->render('not-found.twig');
    }else{
        $body = $view->render('post.twig', ['post' => $post]);
    }
    $response->getBody()->write($body);
    return $response;
});

$app->run();
