<?php
class Database {
    private $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            // Check if running in Render environment
            $url = getenv('DATABASE_URL');

            if ($url) {
                // Render environment: parse the DATABASE_URL
                $db = parse_url($url);

                if (empty($db['host']) || empty($db['port']) || empty($db['user']) || empty($db['pass']) || empty($db['path'])) {
                    throw new Exception("Invalid DATABASE_URL format.");
                }

                // Extract Render database details
                $host = $db['host'];
                $port = $db['port'];
                $db_name = ltrim($db['path'], '/'); // Remove leading slash
                $username = $db['user'];
                $password = $db['pass'];
            } else {
                // Local environment: use custom database settings
                $host = 'localhost';
                $port = '5432';
                $db_name = 'quotesdb'; // Local database name
                $username = 'postgres'; 
                $password = 'postgres'; 
            }

            // Establish the connection
            $this->conn = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db_name",
                $username,
                $password
            );

            // Set error mode to exceptions
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Log the error and display a user-friendly message
            error_log("Database connection error: " . $exception->getMessage());
            echo "Database connection failed. Please try again later.";
        } catch (Exception $exception) {
            // Handle general errors
            error_log("General error: " . $exception->getMessage());
            echo "An error occurred. Please try again later.";
        }

        return $this->conn;
    }
}
?>
