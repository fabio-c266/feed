<?php

namespace src\Config;

use PDO;
use PDOException;

class Database
{
    private static $connection = null;

    public static function connect()
    {
        $hostname = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $dbName = $_ENV['DB_NAME'];

        try {
            $connection = new PDO("mysql:host={$hostname};dbname={$dbName};charset=utf8", $user, $password);
            $connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

            self::$connection = $connection;
        } catch (PDOException $error) {
            exit("Fail to connect the database because: {$error->getMessage()}");
        }
    }

    public static function query(string $queryContent)
    {
        try {
            $connection = self::$connection;
            $result = $connection->prepare($queryContent);
            $result->execute();

            if ($result) {
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                return $rows;
            } else {
                return false;
            }
        } catch (PDOException $error) {
            trigger_error("Fail to execute query because: {$error->getMessage()}");
            exit();
        }
    }
}