<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $host = 'localhost';       // Local environment
            $port = '5432';            // Default port for PostgreSQL
            $db_name = 'quotesdb';     // Your local database name
            $username = 'postgres';    // Your local username
            $password = 'postgres';    // Your local password

            $this->conn = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db_name",
                $username,
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            echo "Database connection failed. Please try again later.";
        }

        return $this->conn;
    }
}
?>
