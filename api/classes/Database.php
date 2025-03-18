<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Step 1: Fetch the DATABASE_URL environment variable
            $url = getenv('DATABASE_URL');
            error_log("DATABASE_URL: " . $url); // Log the DATABASE_URL

            if (!$url) {
                throw new Exception("Environment variable DATABASE_URL not set.");
            }

            // Step 2: Parse the DATABASE_URL
            $db = parse_url($url);
            error_log("Parsed DATABASE_URL: " . print_r($db, true)); // Log the parsed URL

            if (!$db || !isset($db['host'], $db['port'], $db['path'], $db['user'], $db['pass'])) {
                throw new Exception("DATABASE_URL is missing required components.");
            }

            $host = $db['host'];
            $port = $db['port'];
            $db_name = ltrim($db['path'], '/'); // Remove leading slash from database name
            $username = $db['user'];
            $password = $db['pass'];

            // Step 3: Establish a database connection
            $this->conn = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db_name",
                $username,
                $password
            );

            // Step 4: Configure PDO settings for better debugging and performance
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            error_log("DEBUG: Database connection established successfully.");
        } catch (PDOException $exception) {
            error_log("PDO Database connection error: " . $exception->getMessage());
        } catch (Exception $exception) {
            error_log("General error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>