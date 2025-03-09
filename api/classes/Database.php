<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Step 1: Fetch the DATABASE_URL environment variable
            $url = getenv('DATABASE_URL');
            echo "DEBUG: DATABASE_URL = " . ($url ? $url : "NOT SET") . "\n";

            if (!$url) {
                throw new Exception("Environment variable DATABASE_URL not set.");
            }

            // Step 2: Parse the DATABASE_URL
            $db = parse_url($url);
            echo "DEBUG: Parsed URL = " . print_r($db, true) . "\n";

            if (!$db || !isset($db['host'], $db['port'], $db['path'], $db['user'], $db['pass'])) {
                throw new Exception("DATABASE_URL is missing required components.");
            }

            $host = $db['host'];
            $port = $db['port'];
            $db_name = ltrim($db['path'], '/');
            $username = $db['user'];
            $password = $db['pass'];

            echo "DEBUG: Host = $host, Port = $port, Database = $db_name\n";
            echo "DEBUG: Username = $username, Password = [HIDDEN]\n";

            // Step 3: Attempt to establish the connection
            $this->conn = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db_name",
                $username,
                $password
            );

            // Step 4: Set PDO error mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "DEBUG: Database connection established successfully.\n";
        } catch (PDOException $exception) {
            echo "Database connection error: " . $exception->getMessage() . "\n";
        } catch (Exception $exception) {
            echo "General error: " . $exception->getMessage() . "\n";
        }

        return $this->conn;
    }
}
?>
