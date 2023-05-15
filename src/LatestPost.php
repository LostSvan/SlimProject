<?php

namespace Blog;

use PDO;

class LatestPost
{
    private Database $database;
    public function __construct(Database $database) {
        $this->database = $database;
    }
    public function get(int $limit): ?array {
        $stmt = $this->database->getConnect()->prepare("SELECT * FROM post ORDER BY published_date DESC LIMIT $limit");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}