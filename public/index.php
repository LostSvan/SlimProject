<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Blog\Postmapper;

require __DIR__ . '/../vendor/autoload.php';
$config = include '../config/database.php';
$app = AppFactory::create();
$loader = new FilesystemLoader('../templates');
$view = new Environment($loader);

try {
    $connect = new PDO($config['dsn'], $config['username'], $config['password']);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit();
}
$postObject = new Postmapper($connect);

$app->get('/', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('index.twig');
    $response->getBody()->write($body);
    return $response;
});
$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig', ['name' => 'Vlad']);
    $response->getBody()->write($body);
    return $response;
});
$app->get('/post/{url_key}', function (Request $request, Response $response, $args) use ($postObject, $view) {
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
