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
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read one category
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->category = $row['category'];
        }
    }

    // Create a category
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (category) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->category);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update a category
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET category = :category WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete a category
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>