<?php

namespace Blog;

use Exception;
use PDO;

class Postmapper
{
    private PDO $connect;

    public function __construct(PDO $connect)
    {
        $this->connect = $connect;
    }

    public function getByUrlKey(string $urlKey): ?array {
        $stmt = $this->connect->prepare("SELECT * FROM post WHERE url_key = :url_key");
        $stmt->execute([
           'url_key' => $urlKey,
        ]);
        $result = $stmt->fetchAll();
        return array_shift($result);
    }
    public function getList(int $page = 1, int $limit = 2,string $order = 'ASC') {
        if(!in_array($order, ['ASC', 'DESC'])) {
            throw new Exception('The direction is not supported');
        }
        $start = ($page - 1) * $limit;
        $stmt = $this->connect->prepare("SELECT * FROM post ORDER BY published_date $order LIMIT $start, $limit");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getCountList(): int {
        $stmt = $this->connect->prepare("SELECT count(post_id) as total FROM post");
        $stmt->execute();
        return (int) ($stmt->fetchColumn() ?? 0);
    }
}