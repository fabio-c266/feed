<?php

namespace src\config;

use Exception;
use PDO;
use PDOException;

class Database
{
    private static $connection = null;

    public static function connect()
    {
        $hostname = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $dbName = $_ENV['DB_NAME'];

        try {
            $connection = new PDO("mysql:host={$hostname};dbname={$dbName};charset=utf8", $user, $password);
            $connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

            self::$connection = $connection;
        } catch (PDOException $error) {
            throw new Exception("Failed to connect to the database: {$error->getMessage()}");
        }
    }

    public static function query(string $queryContent)
    {
        try {
            $connection = self::$connection;
            $result = $connection->prepare($queryContent);

            if ($result->execute()) {
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                return $rows;
            }
        } catch (PDOException $error) {
            throw new Exception("Fail to execute query because: {$error->getMessage()}");
        }
    }
}
