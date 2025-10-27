<?php
/**
 * Encapsulates a connection to the database 
 * 
 * @author  Arturo Mora-Rioja
 * @version 1.0 August 2020
 */
require_once 'info/Info.php';

class DB {    
    protected object $pdo;

    /**
     * Opens a connection to the database
     */
    public function __construct() {
        $dsn = 'mysql:host=' . Info::$HOST . ';port=' . Info::$PORT . ';dbname=' . Info::$DB_NAME . ';charset=utf8';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->pdo = new PDO($dsn, Info::$USER, Info::$PASSWORD, $options); 
        } catch (\PDOException $e) {
            $errorMsg = 'Database connection failed: ' . $e->getMessage() . 
                       ' (Host: ' . Info::$HOST . ', Port: ' . Info::$PORT . ', Database: ' . Info::$DB_NAME . ')';
            error_log($errorMsg);
            throw new \PDOException($errorMsg, $e->getCode(), $e);
        }
    }

    /**
     * Closes a connection to the database
     */
    public function __destruct() {
        unset($this->pdo);
    }
}
?>