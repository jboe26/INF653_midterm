<?php
class Author {
    private $conn;
    private $table_name = "authors";

    public $id;
    public $author;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all authors
    public function read() {
        try {
            $query = "SELECT id, author FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database error (read): " . $e->getMessage());
            return false;
        }
    }

    // Read one author by ID
    public function readOne() {
        try {
            $query = "SELECT id, author FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $this->author = $row['author'];
                return true;
            } else {
                return false; // Author not found
            }
        } catch (PDOException $e) {
            error_log("Database error (readOne): " . $e->getMessage());
            return false;
        }
    }
    
        // Create an author
        public function create() {
            try {
                $query = "INSERT INTO " . $this->table_name . " (author) VALUES (:author)";
                $stmt = $this->conn->prepare($query);

                // Sanitize input
                $this->author = htmlspecialchars(strip_tags($this->author));

                // Bind parameter
                $stmt->bindParam(':author', $this->author);

                if ($stmt->execute()) {
                    // Get the last inserted ID and set it to $this->id
                    $this->id = $this->conn->lastInsertId();
                    return true;
                } else {
                    return false;
                }

            } catch (PDOException $e) {
                error_log("Database error (create): " . $e->getMessage());
                return false;
            }
        }

    // Update an author
    public function update() {
        try {
            $query = "UPDATE " . $this->table_name . " SET author = :author WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->author = htmlspecialchars(strip_tags($this->author));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind parameters
            $stmt->bindParam(':author', $this->author);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error (update): " . $e->getMessage());
            return false;
        }
    }

    // Delete an author
    public function delete() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Sanitize ID
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind parameter
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error (delete): " . $e->getMessage());
            return false;
        }
    }
}
?>
