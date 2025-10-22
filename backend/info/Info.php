<?php
class Info
{
    public static string $HOST;
    public static string $DB_NAME;
    public static string $USER;
    public static string $PASSWORD;
    
    public static function init(): void
    {
        self::$HOST = $_ENV['DB_HOST'] ?? 'mariadb';
        self::$DB_NAME = $_ENV['DB_NAME'] ?? 'addresses';
        self::$USER = $_ENV['DB_USER'] ?? 'root';
        self::$PASSWORD = $_ENV['DB_PASSWORD'] ?? 'password';
    }
}

// Initialize when the class is loaded
Info::init();
