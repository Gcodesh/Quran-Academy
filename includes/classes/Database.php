<?php

require_once __DIR__ . '/../config/database.php';

class Database {
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            // Create temporary certificate file for TiDB Cloud SSL
            $certContent = file_get_contents(__DIR__ . '/../config/ca-cert.pem');
            $tempCertPath = sys_get_temp_dir() . '/tidb-ca-cert.pem';
            file_put_contents($tempCertPath, $certContent);
            
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_SSL_CA => $tempCertPath,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function close() {
        $this->conn = null;
    }
}

?>