<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Step 1: Fetch the DATABASE_URL environment variable
            $url = getenv('DATABASE_URL');

            if (!$url) {
                throw new Exception("Environment variable DATABASE_URL not set.");
            }

            // Step 2: Parse the DATABASE_URL
            $db = parse_url($url);
            error_log("DEBUG: Parsed URL = " . print_r($db, true)); 

            if (!$db || !isset($db['host'], $db['port'], $db['path'], $db['user'], $db['pass'])) {
                throw new Exception("DATABASE_URL is missing required components.");
            }

            $host = $db['host'];
            $port = $db['port'];
            $db_name = ltrim($db['path'], '/');
            $username = $db['user'];
            $password = $db['pass'];

            error_log("DEBUG: Host = $host, Port = $port, Database = $db_name");
            error_log("DEBUG: Username = $username, Password = [HIDDEN]"); 

            // Step 3: Attempt to establish the connection
            $this->conn = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db_name",
                $username,
                $password
            );

            // Step 4: Set PDO error mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            error_log("DEBUG: Database connection established successfully."); 
        } catch (PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage()); 
        } catch (Exception $exception) {
            error_log("General error: " . $exception->getMessage()); 
        }

        return $this->conn;
    }
}
?>
