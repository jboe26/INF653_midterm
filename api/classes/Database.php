<?php
class Database {
    private $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            // Use the Render DATABASE_URL
            $url = getenv('DATABASE_URL');

            if (!$url) {
                throw new Exception("Environment variable DATABASE_URL not set.");
            }

            // Parse the DATABASE_URL
            $db = parse_url($url);

            // Extract components from the URL
            $host = $db['host'];
            $port = $db['port'];
            $db_name = ltrim($db['path'], '/'); // Remove leading slash from path
            $username = $db['user'];
            $password = $db['pass'];

            // Establish the connection
            $this->conn = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db_name",
                $username,
                $password
            );

            // Set error mode to exceptions
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Database connection error: " . $exception->getMessage();
        } catch (Exception $exception) {
            echo "General error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>