<?php
class Quote {
    // Database connection and table name
    private $conn;
    private $table_name = "quotes";

    // Object properties
    public $id;
    public $quote;
    public $author_id;
    public $category_id;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all quotes
    public function read() {
        try {
            $query = "SELECT q.id, q.quote, a.author AS author, c.category AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Convert data directly to an array
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // Read one quote by ID
    public function readOne() {
        try {
            $query = "SELECT q.id, q.quote, a.name AS author, c.name AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id 
                      WHERE q.id = ? LIMIT 0,1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC); // Return a single result
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // Create a quote
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                      SET quote=:quote, author_id=:author_id, category_id=:category_id";
            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->author_id = htmlspecialchars(strip_tags($this->author_id));
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));

            // Bind parameters
            $stmt->bindParam(":quote", $this->quote);
            $stmt->bindParam(":author_id", $this->author_id);
            $stmt->bindParam(":category_id", $this->category_id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // Update a quote
    public function update() {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET quote = :quote, author_id = :author_id, category_id = :category_id 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->author_id = htmlspecialchars(strip_tags($this->author_id));
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind parameters
            $stmt->bindParam(":quote", $this->quote);
            $stmt->bindParam(":author_id", $this->author_id);
            $stmt->bindParam(":category_id", $this->category_id);
            $stmt->bindParam(":id", $this->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // Delete a quote
    public function delete() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);

            // Sanitize ID
            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1, $this->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // Read a random quote
    public function readRandom() {
        try {
            $query = "SELECT q.id, q.quote, a.name AS author, c.name AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id 
                      ORDER BY RAND() LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC); // Return a single random quote
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
}
?>
