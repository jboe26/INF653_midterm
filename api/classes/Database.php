<?php
class Database {
    // Database credentials
    private $host = "localhost";
    private $db_name = "quotesdb";
    private $username = "postgres";  // My PostgreSQL username
    private $password = "postgres"; // My PostgreSQL password

    public $conn;

    // Get the database connection
    public function getConnection(){
        $this->conn = null;
        try {
            $this->conn = new PDO("pgsql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>