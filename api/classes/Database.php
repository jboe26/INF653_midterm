<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Use Render's environment variable
            $url = getenv('DATABASE_URL');
            if ($url) {
                $db = parse_url($url);
                $host = $db['host'];
                $port = $db['port'];
                $db_name = ltrim($db['path'], '/'); // Trim leading slash from database name
                $username = $db['user'];
                $password = $db['pass'];
            } else {
                // Local Development Fallback
                $host = 'localhost';      
                $port = '5432';            
                $db_name = 'quotesdb';     
                $username = 'postgres';    
                $password = 'postgres';    
            }

            // Connect to PostgreSQL
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
