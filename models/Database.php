<?php
class database {
    private $connection;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';

        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>