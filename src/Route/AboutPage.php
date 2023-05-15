<?php

namespace Blog\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class AboutPage
{
    public Environment $view;
    public function __construct(Environment $view) {
        $this->view = $view;
    }
    public function __invoke(Request $request, Response $response, $args) {
        $body = $this->view->render('about.twig', ['name' => 'Vlad']);
        $response->getBody()->write($body);
        return $response;
    }
}