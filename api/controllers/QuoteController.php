<?php

include_once __DIR__ . '/../classes/Database.php';
include_once __DIR__ . '/../classes/Quote.php';

class QuoteController {
    private $db;
    private $quote;

    public function __construct($db) {
        $this->db = $db;
        $this->quote = new Quote($db);
    }

    // Fetch all quotes
    public function fetchQuotes() {
        $result = $this->quote->read(); // Call the model's read method to fetch quotes
    
        $num = $result->rowCount(); // Get the count of rows (correct variable is $result)
    
        if ($num > 0) {
            $quotes_arr = []; // Initialize an array to hold quotes
    
            // Loop through results and build the JSON response
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $quotes_arr[] = [
                    "id" => $row["id"],
                    "quote" => $row["quote"],
                    "author" => $row["author"], 
                    "category" => $row["category"] 
                ];
            }
    
            // Send the array of quotes as JSON
            http_response_code(200); // OK
            echo json_encode($quotes_arr);
            return;
        }
    
        // If no quotes are found, return a message
        http_response_code(404); // Not Found
        echo json_encode(["message" => "No Quotes Found."]);
    }    

    // Handle incoming requests
    public function handleRequest($method, $params) {
        switch ($method) {
            case 'GET':
                $this->handleGet($params);
                return;
            case 'POST':
                $this->handlePost();
                return;
            case 'PUT':
                $this->handlePut();
                return;
            case 'DELETE':
                $this->handleDelete();
                return;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['message' => 'Method not allowed.']);
                return;
        }
    }

    private function handleGet($params) {
        if (isset($_GET['id'])) { // Check if 'id' is provided
            $this->quote->id = htmlspecialchars(strip_tags($_GET['id']));
            $result = $this->quote->read_single();

            if ($result) {
                http_response_code(200); // OK
                echo json_encode([
                    "id" => $this->quote->id,
                    "quote" => $this->quote->quote,
                    "author" => $this->quote->author,
                    "category" => $this->quote->category
                ]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "No Quote Found."]);
            }
            return;
        }

        if (isset($_GET['author_id']) && isset($_GET['category_id'])) {
            $author_id = htmlspecialchars(strip_tags($_GET['author_id']));
            $category_id = htmlspecialchars(strip_tags($_GET['category_id']));
            $result = $this->quote->readByAuthorAndCategory($author_id, $category_id);

            $quotes_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $quotes_arr[] = [
                    "id" => $row["id"],
                    "quote" => $row["quote"],
                    "author" => $row["author"],
                    "category" => $row["category"]
                ];
            }

            if (count($quotes_arr) > 0) {
                http_response_code(200); // OK
                echo json_encode($quotes_arr);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "No Quotes Found for this Author and Category."]);
            }
            return;
        }

        if (isset($_GET['author_id'])) {
            $author_id = htmlspecialchars(strip_tags($_GET['author_id']));
            $result = $this->quote->readByAuthor($author_id);

            $quotes_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $quotes_arr[] = [
                    "id" => $row["id"],
                    "quote" => $row["quote"],
                    "author" => $row["author"],
                    "category" => $row["category"]
                ];
            }

            if (count($quotes_arr) > 0) {
                http_response_code(200); // OK
                echo json_encode($quotes_arr);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "No Quotes Found for this Author."]);
            }
            return;
        }

        if (isset($_GET['category_id'])) {
            $category_id = htmlspecialchars(strip_tags($_GET['category_id']));
            $result = $this->quote->readByCategory($category_id);

            $quotes_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $quotes_arr[] = [
                    "id" => $row["id"],
                    "quote" => $row["quote"],
                    "author" => $row["author"],
                    "category" => $row["category"]
                ];
            }

            if (count($quotes_arr) > 0) {
                http_response_code(200); // OK
                echo json_encode($quotes_arr);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "No Quotes Found for this Category."]);
            }
            return;
        }

        // If no filters are provided, return all quotes
        $this->fetchQuotes();
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $this->quote->quote = htmlspecialchars(strip_tags($data->quote));
            $this->quote->author_id = htmlspecialchars(strip_tags($data->author_id));
            $this->quote->category_id = htmlspecialchars(strip_tags($data->category_id));

            $authorExists = $this->authorExists($this->quote->author_id);
            $categoryExists = $this->categoryExists($this->quote->category_id);

            if ($authorExists && $categoryExists) {
                if ($this->quote->create()) {
                    http_response_code(201); // Created
                    echo json_encode(["message" => "Quote was created."]);
                    return;
                } else {
                    http_response_code(503); // Service Unavailable
                    echo json_encode(["message" => "Unable to create quote."]);
                    return;
                }
            } else {
                http_response_code(400); // Bad Request
                $message = "";
                if (!$authorExists) $message .= "author_id Not Found. ";
                if (!$categoryExists) $message .= "category_id Not Found.";
                echo json_encode(["message" => $message]);
                return;
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $this->quote->id = htmlspecialchars(strip_tags($data->id));
            $this->quote->quote = htmlspecialchars(strip_tags($data->quote));
            $this->quote->author_id = htmlspecialchars(strip_tags($data->author_id));
            $this->quote->category_id = htmlspecialchars(strip_tags($data->category_id));

            if ($this->quote->update()) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Quote was updated."]);
                return;
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to update quote."]);
                return;
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to update quote. Data is incomplete."]);
            return;
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            $this->quote->id = htmlspecialchars(strip_tags($data->id));

            if ($this->quote->delete()) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Quote was deleted."]);
                return;
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to delete quote."]);
                return;
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to delete quote. Data is incomplete."]);
            return;
        }
    }

    private function authorExists($author_id) {
        if (!is_numeric($author_id)) return false; // Ensure it's numeric
        $query = "SELECT id FROM authors WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $author_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    private function categoryExists($category_id) {
        if (!is_numeric($category_id)) return false; // Ensure it's numeric
        $query = "SELECT id FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>

