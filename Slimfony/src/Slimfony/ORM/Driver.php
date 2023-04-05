<?php

namespace Slimfony\ORM;

use PDO;

class Driver
{
    protected $connection;
    public function __construct(
        string $url,
        string $username,
        string $password,
    ) {
        $this->connection = new PDO($url, $username, $password);
    }

    public function beginTransaction() {
        $this->connection->beginTransaction();
    }

    public function inTransaction(): bool
    {
        $this->connection->inTransaction();
    }

    public function execute(string $sql, $parameters=[]): array
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function rollBack()
    {
        $this->connection->rollBack();
    }

    public function commit()
    {
        $this->connection->commit();
    }
}