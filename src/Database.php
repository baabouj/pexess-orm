<?php

namespace Pexess\ORM;

class Database
{
    private \PDO $pdo;

    private \PDOStatement|false $statement;

    private static ?Database $db = null;

    private function __construct()
    {
        $this->pdo = Connector::connect();
    }

    public static function instance(): Database
    {
        if (!self::$db) {
            self::$db = new Database();
        }
        return self::$db;
    }

    public static function from($table): QueryBuilder
    {
        return new QueryBuilder($table);
    }

    public function query($sql)
    {
        $this->statement = $this->pdo->prepare($sql);
    }

    public function bind($parameter, $value, $type = null)
    {
        $type = match (is_null($type)) {
            is_int($value) => \PDO::PARAM_INT,
            is_bool($value) => \PDO::PARAM_BOOL,
            is_null($value) => \PDO::PARAM_NULL,
            default => \PDO::PARAM_STR,
        };
        $this->statement->bindValue($parameter, $value, $type);
    }

    public function execute(array $bindings = []): bool
    {
        return $this->statement->execute($bindings);
    }

    public function result(): bool|array
    {
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function resultSet(array $bindings = []): bool|array
    {
        $this->execute($bindings);
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function single(array $bindings = [])
    {
        $this->execute($bindings);
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

}