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

            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all results
        } catch (PDOException $e) {
            error_log("Database error (read): " . $e->getMessage());
            return false;
        }
    }

    // Read one quote by ID
    public function readOne() {
        try {
            $query = "SELECT q.id, q.quote, a.author AS author, c.category AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id 
                      WHERE q.id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC); // Return a single result
        } catch (PDOException $e) {
            error_log("Database error (readOne): " . $e->getMessage());
            return false;
        }
    }

    // Create a quote
    public function create() {
        try {
            // Validate required fields
            if (empty($this->quote) || empty($this->author_id) || empty($this->category_id)) {
                error_log("Validation error: Missing required fields.");
                return false;
            }
            
            // Check for duplicate quote
            if ($this->isDuplicate()) {
                error_log("Duplicate quote detected: " . $this->quote);
                return false;
            }

            $query = "INSERT INTO " . $this->table_name . " 
                      (quote, author_id, category_id) 
                      VALUES (:quote, :author_id, :category_id)";
            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->author_id = htmlspecialchars(strip_tags($this->author_id));
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));

            // Bind parameters
            $stmt->bindParam(":quote", $this->quote);
            $stmt->bindParam(":author_id", $this->author_id);
            $stmt->bindParam(":category_id", $this->category_id);

            // Execute query
            if ($stmt->execute()) {
                error_log("Quote created successfully: " . $this->quote);
                return true;
            } else {
                error_log("Failed to create quote: " . json_encode($stmt->errorInfo()));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Database error (create): " . $e->getMessage());
            return false;
        }
    }

    // Check for duplicate quote
    public function isDuplicate() {
        try {
            $query = "SELECT id FROM " . $this->table_name . " WHERE quote = :quote";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":quote", $this->quote);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Database error (isDuplicate): " . $e->getMessage());
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
            error_log("Database error (update): " . $e->getMessage());
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
            error_log("Database error (delete): " . $e->getMessage());
            return false;
        }
    }

    // Read a random quote
    public function readRandom() {
        try {
            $query = "SELECT q.id, q.quote, a.author AS author, c.category AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id 
                      ORDER BY RAND() LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC); // Return a single random quote
        } catch (PDOException $e) {
            error_log("Database error (readRandom): " . $e->getMessage());
            return false;
        }
    }
}
?>
