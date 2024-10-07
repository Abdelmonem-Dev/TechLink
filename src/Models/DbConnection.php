<?php

class DbConnection
{
    private static $host = 'localhost';
    private static $db_name = 'techlink';
    private static $username = 'root';
    private static $password = '';
    private static $conn = NULL;

    public static function getConnection()
    {
        if (self::$conn === NULL) {
            try {
                self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$db_name, self::$username, self::$password);
                self::$conn->exec("set names utf8");
            } catch (PDOException $exception) {
                error_log("Connection error: " . $exception->getMessage());
                exit(); 
            }
        }

        return self::$conn;
    }

    public static function Close()
    {
        self::$conn = null;
    }
}

