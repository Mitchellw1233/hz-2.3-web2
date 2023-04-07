<?php

namespace Slimfony\ORM;

use PDO;
use Slimfony\Config\ConfigLoader;

class Driver
{
    protected $connection;
    public function __construct(
        ConfigLoader $config,
    ) {
        $config = $config->getDb();

        $host = $config['host'];
        $port = $config['port'];
        $db = $config['database'];

        $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
        $this->connection = new PDO($dsn, $config['username'], $config['password']);
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