<?php

use Blog\LatestPost;
use Blog\twig\AssetExtension;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Blog\Postmapper;

require __DIR__ . '/../vendor/autoload.php';
$config = include '../config/database.php';

try {
    $connect = new PDO($config['dsn'], $config['username'], $config['password']);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit();
}
//$loader = new FilesystemLoader('../templates');
//$view = new Environment($loader);

$builder = new ContainerBuilder();
$builder->addDefinitions('../config/di.php');
$container = $builder->build();

AppFactory::setContainer($container);
$view = $container->get(Environment::class);

$app = AppFactory::create();
$app->add(new \Blog\slim\TwigMiddleware($view));
$app->get('/', function (Request $request, Response $response, $args) use ($view, $connect) {
    $latestPost = new LatestPost($connect);
    $posts = $latestPost->get(3);
    $body = $view->render('index.twig', ['posts' => $posts]);
    $response->getBody()->write($body);
    return $response;
});
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
