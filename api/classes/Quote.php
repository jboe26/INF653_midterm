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
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read one quote by ID
    public function readOne(){
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id 
                  WHERE q.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Check if $row is not empty before accessing its elements
        if ($row) {
            $this->quote = $row['quote'];
            $this->author_id = $row['author'];  // Assuming 'author' is the author's id
            $this->category_id = $row['category'];  // Assuming 'category' is the category's id
        } else {
            // Handle the case where no quote is found
            $this->quote = null;
            $this->author_id = null;
            $this->category_id = null;
        }
    }

    // Read quotes by author ID
    public function readByAuthor($author_id) {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id 
                  WHERE q.author_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $author_id);
        $stmt->execute();
        return $stmt;
    }

    // Read quotes by category ID
    public function readByCategory($category_id) {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id 
                  WHERE q.category_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();
        return $stmt;
    }

    // Read quotes by author ID and category ID
    public function readByAuthorAndCategory($author_id, $category_id) {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id 
                  WHERE q.author_id = ? AND q.category_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $author_id);
        $stmt->bindParam(2, $category_id);
        $stmt->execute();
        return $stmt;
    }

    // Read a random quote
    public function readRandom() {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id 
                  ORDER BY RAND() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read a random quote by author ID
    public function readRandomByAuthor($author_id) {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id 
                  WHERE q.author_id = ? 
                  ORDER BY RAND() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $author_id);
        $stmt->execute();
        return $stmt;
    }

    // Read a random quote by category ID
    public function readRandomByCategory($category_id) {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id 
                  WHERE q.category_id = ? 
                  ORDER BY RAND() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();
        return $stmt;
    }

    // Read a random quote by author ID and category ID
    public function readRandomByAuthorAndCategory($author_id, $category_id) {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM " . $this->table_name . " q 
                  LEFT JOIN authors a ON q.author_id = a.id 
                  LEFT JOIN categories c ON q.category_id = c.id 
                  WHERE q.author_id = ? AND q.category_id = ? 
                  ORDER BY RAND() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $author_id);
        $stmt->bindParam(2, $category_id);
        $stmt->execute();
        return $stmt;
    }

    // Create a quote
    public function create(){
        $query = "INSERT INTO " . $this->table_name . " 
                  SET quote=:quote, author_id=:author_id, category_id=:category_id";
        $stmt = $this->conn->prepare($query);
        $this->quote=htmlspecialchars(strip_tags($this->quote));
        $this->author_id=htmlspecialchars(strip_tags($this->author_id));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));

        $stmt->bindParam(":quote", $this->quote);
        $stmt->bindParam(":author_id", $this->author_id);
        $stmt->bindParam(":category_id", $this->category_id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // Update a quote
    public function update(){
        $query = "UPDATE " . $this->table_name . " 
                  SET quote = :quote, author_id = :author_id, category_id = :category_id 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->quote=htmlspecialchars(strip_tags($this->quote));
        $this->author_id=htmlspecialchars(strip_tags($this->author_id));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":quote", $this->quote);
        $stmt->bindParam(":author_id", $this->author_id);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // Delete a quote
    public function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()){
            return true;
        }

        return false;
    }
}
?>