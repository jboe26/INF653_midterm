<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $url = getenv('DATABASE_URL');
            if (!$url) {
                throw new Exception("Environment variable DATABASE_URL not set.");
            }

            $db = parse_url($url);
            $host = $db['host'];
            $port = $db['port'];
            $db_name = ltrim($db['path'], '/');
            $username = $db['user'];
            $password = $db['pass'];

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
