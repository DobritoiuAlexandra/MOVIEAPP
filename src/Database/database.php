<?php
// src/Database/database.php

class Database
{
    private static $pdo;

    /**
     * @throws Exception
     */
    public static function getInstance(): PDO
    {
        if (!isset(self::$pdo)) {
            $config = require_once(__DIR__ . '/../security/config.php');
            $databaseArray = $config['database'];
            $dbHost = $databaseArray['dbHost'];
            $dbName = $databaseArray['dbName'];
            $dbUser = $databaseArray['dbUser'];
            $dbPass = $databaseArray['dbPass'];

            try {
                self::$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception('Eroare la conectarea la baza de date: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}

try {
    return Database::getInstance();
} catch (Exception $e) {
    echo ('Nu s-a putut returna database instance ' . $e->getMessage());
}

