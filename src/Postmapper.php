<?php

namespace Blog;

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
}