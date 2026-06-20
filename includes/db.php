<?php
/**
 * Database Connection Class
 * Secure PDO Connection with Prepared Statements
 */

class Database {
    private static $instance = null;
    private $connection;
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;

    public function __construct() {
        $this->connect();
    }

    public function connect() {
        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';
            $this->connection = new PDO(
                $dsn,
                $this->user,
                $this->pass,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                )
            );
            return $this->connection;
        } catch (PDOException $e) {
            die('Database Connection Error: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = array()) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database Query Error: ' . $e->getMessage());
            return false;
        }
    }

    public function fetch($sql, $params = array()) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : null;
    }

    public function fetchAll($sql, $params = array()) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : array();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollback() {
        return $this->connection->rollBack();
    }

    public function rowCount($sql, $params = array()) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->rowCount() : 0;
    }
}

$db = Database::getInstance();
?>
