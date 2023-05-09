<?php

namespace Blog;

use PDO;

class LatestPost
{
    private PDO $connect;
    public function __construct(PDO $connect) {
        $this->connect = $connect;
    }
    public function get(int $limit): ?array {
        $stmt = $this->connect->prepare("SELECT * FROM post ORDER BY published_date DESC LIMIT $limit");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}