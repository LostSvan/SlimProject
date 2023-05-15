<?php

namespace Blog\Route;
use Blog\Postmapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class PostPage
{
    private Environment $view;
    private Postmapper $postObject;
    public function __construct(Environment $view, Postmapper $postObject){
        $this->view = $view;
        $this->postObject = $postObject;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $post = $this->postObject->getByUrlKey($args['url_key']);
        if (empty($post)) {
            $body = $this->view->render('not-found.twig');
        }else{
        $body = $this->view->render('post.twig', ['post' => $post]);
        }
        $response->getBody()->write($body);
        return $response;
    }

}