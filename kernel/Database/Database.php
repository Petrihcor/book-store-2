<?php

namespace Kernel\Database;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
class Database
{
    private ?Connection $conn = null;

    public function __construct(
        private array $config
    )
    {
    }

    public function connect()
    {
        try {
            $this->getConn();
        } catch (\Doctrine\DBAL\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getConn(): Connection
    {
        if ($this->conn === null) {
            $this->conn = DriverManager::getConnection($this->config);
        }
        return $this->conn;
    }

    public function getBuilder()
    {
        return $this->getConn()->createQueryBuilder();
    }
    
}