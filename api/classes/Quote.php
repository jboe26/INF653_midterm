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

            return $stmt; // Return the PDO statement object
        } catch (PDOException $e) {
            error_log("Database error (read): " . $e->getMessage());
            return false;
        }
    }

    // Read one quote by ID
    public function read_single() {
        try {
            $query = "SELECT q.id, q.quote, a.author AS author, c.category AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id 
                      WHERE q.id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC); // Return a single result
        } catch (PDOException $e) {
            error_log("Database error (readOne): " . $e->getMessage());
            return false;
        }
    }

    // Read quotes by author ID
    public function readByAuthor($author_id) {
        try {
            $query = "SELECT q.id, q.quote, a.author AS author, c.category AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id 
                      WHERE q.author_id = :author_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':author_id', $author_id);
            $stmt->execute();

            return $stmt; // Return the statement for use in the controller
        } catch (PDOException $e) {
            error_log("Database error (readByAuthor): " . $e->getMessage());
            return false;
        }
    }

    // Read quotes by category ID
    public function readByCategory($category_id) {
        try {
            $query = "SELECT q.id, q.quote, a.author AS author, c.category AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id 
                      WHERE q.category_id = :category_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();

            return $stmt;
        } catch (PDOException $e) {
            error_log("Database error (readByCategory): " . $e->getMessage());
            return false;
        }
    }

    // Read quotes by both author and category IDs
    public function readByAuthorAndCategory($author_id, $category_id) {
        try {
            $query = "SELECT q.id, q.quote, a.author AS author, c.category AS category 
                      FROM " . $this->table_name . " q 
                      LEFT JOIN authors a ON q.author_id = a.id 
                      LEFT JOIN categories c ON q.category_id = c.id 
                      WHERE q.author_id = :author_id AND q.category_id = :category_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':author_id', $author_id);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();

            return $stmt;
        } catch (PDOException $e) {
            error_log("Database error (readByAuthorAndCategory): " . $e->getMessage());
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

            // Check if author and category exist
            if (!$this->authorExists($this->author_id) || !$this->categoryExists($this->category_id)) {
                error_log("Validation error: Invalid author_id or category_id.");
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

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error (create): " . $e->getMessage());
            return false;
        }
    }

    // Helper function to check if the author exists
    private function authorExists($author_id) {
        $query = "SELECT id FROM authors WHERE id = :author_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Helper function to check if the category exists
    private function categoryExists($category_id) {
        $query = "SELECT id FROM categories WHERE id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
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

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $row; // Return the random quote
            } else {
                return false; // No quotes available
            }
        } catch (PDOException $e) {
            error_log("Database error (readRandom): " . $e->getMessage());
            return false;
        }
    }
}
?>
