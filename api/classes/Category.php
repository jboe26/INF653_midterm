<?php
class Category {
    private $conn;
    private $table_name = "categories";

    public $id;
    public $category;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all categories
    public function read() {
        try {
            $query = "SELECT id, category FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database error (read): " . $e->getMessage());
            return false;
        }
    }

    // Read one category by ID
    public function readOne() {
        try {
            $query = "SELECT id, category FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $this->category = $row['category'];
                return true;
            } else {
                return false; // Category not found
            }
        } catch (PDOException $e) {
            error_log("Database error (readOne): " . $e->getMessage());
            return false;
        }
    }

    // Create a category
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . " (category) VALUES (:category)";
            $stmt = $this->conn->prepare($query);

            // Sanitize input
            $this->category = htmlspecialchars(strip_tags($this->category));

            // Bind parameter
            $stmt->bindParam(':category', $this->category);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error (create): " . $e->getMessage());
            return false;
        }
    }

    // Update a category
    public function update() {
        try {
            $query = "UPDATE " . $this->table_name . " SET category = :category WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->category = htmlspecialchars(strip_tags($this->category));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind parameters
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error (update): " . $e->getMessage());
            return false;
        }
    }

    // Delete a category
    public function delete() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Sanitize input
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
