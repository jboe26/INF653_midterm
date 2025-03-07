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
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read one author
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->author = $row['author'];
        }
    }

    // Create an author
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (author) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->author);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update an author
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET author = :author WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete an author
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