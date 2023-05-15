<?php

namespace Blog\Route;
use Blog\LatestPost;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Blog\Database;
use Twig\Environment;

class HomePage
{
    private LatestPost $latestPost;
    private Environment $view;
    public function __construct(LatestPost $latestPost, Environment $view) {
        $this->latestPost = $latestPost;
        $this->view = $view;
    }
    public function execute(Request $request, Response $response, $args) {
        $posts = $this->latestPost->get(3);
        $body = $this->view->render('index.twig', ['posts' => $posts]);
        $response->getBody()->write($body);
        return $response;
    }
}