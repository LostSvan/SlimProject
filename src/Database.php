<?php

namespace Blog;

use http\Exception\InvalidArgumentException;
use PDO;
use PDOException;

class Database
{
    public PDO $connect;
    public function __construct(PDO $connect)
    {
        try {
            $this->connect = $connect;
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    public function getConnect():PDO {
        return $this->connect;
    }

}