<?php

namespace Blog\Route;
use Blog\LatestPost;
use Blog\Postmapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class BlogPage
{
    public Environment $view;
    public Postmapper $listPost;
    public function __construct(Environment $view, Postmapper $listPost){
        $this->view = $view;
        $this->listPost = $listPost;
    }

    public function __invoke(Request $request, Response $response, $args) {
    //    current это текущий
    //    paging это коло-во страниц
        $page = isset($args['page']) ? (int)$args['page'] : 1;
        $limit = 2;
        $totalCount = ($this->listPost->getCountList());
        $posts = $this->listPost->getList($page, $limit, 'DESC');
        $body = $this->view->render('blog.twig', [
            'posts' => $posts,
            'current' => $page,
            'paging' => ceil($totalCount / $limit),
            ]);
        $response->getBody()->write($body);
        return $response;
    }
}