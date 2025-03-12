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

    public function fetchQuotes() {
        $result = $this->quote->read();
    
        if (!$is_array($result) || count($result) === 0) {
            error_log("Warning: No valid data fetched from database.");
            return ["status" => "error", "message" => "No quotes available."];
        }
        
        $quotes = [];
        foreach ($result as $row) {
            $quote_item = [
                "id" => $row['id'],
                "quote" => $row['quote'],
                "author" => $row['author'],
                "category" => $row['category']
            ];
            $quotes[] = $quote_item;
        }
    
        return ["status" => "success", "data" => $quotes];
    }
    
    
    public function handleRequest($method, $params) {
        switch ($method) {
            case 'GET':
                $this->handleGet($params);
                break;
            case 'POST':
                $this->handlePost();
                break;
            case 'PUT':
                $this->handlePut();
                break;
            case 'DELETE':
                $this->handleDelete();
                break;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['message' => 'Method not allowed.']);
        }
    }

    private function handleGet($params) {
        if (isset($params[0])) { // Check if an ID is provided
            $this->quote->id = $params[0];
            $this->quote->readOne();

            if ($this->quote->quote != null) {
                $quote_arr = array(
                    "id" => $this->quote->id,
                    "quote" => $this->quote->quote,
                    "author" => $this->quote->author_id, // Assuming this is the author's name now
                    "category" => $this->quote->category_id // Assuming this is the category name now
                );
                http_response_code(200);
                echo json_encode($quote_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Quote Not Found."));
            }
        } else { // No ID provided, get all quotes
            $stmt = $this->quote->read();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $quotes_arr = array();
                $quotes_arr["quotes"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Extract data correctly
                    $quote_item = array(
                        "id" => $row['id'],
                        "quote" => $row['quote'],
                        "author" => $row['author'],
                        "category" => $row['category']
                    );
                    array_push($quotes_arr["quotes"], $quote_item);
                }

                http_response_code(200);
                echo json_encode($quotes_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No Quotes Found."));
            }
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));

        // Validate required parameters
        if (
            !empty($data->quote) && 
            !empty($data->author_id) && 
            !empty($data->category_id)
        ) {
            // Check if author_id and category_id exist
            $authorExists = $this->authorExists($data->author_id);
            $categoryExists = $this->categoryExists($data->category_id);

            if ($authorExists && $categoryExists) {
                $this->quote->quote = $data->quote;
                $this->quote->author_id = $data->author_id;
                $this->quote->category_id = $data->category_id;

                if ($this->quote->create()) {
                    http_response_code(201); // Created
                    echo json_encode(array("message" => "Quote was created."));
                } else {
                    http_response_code(503); // Service Unavailable
                    echo json_encode(array("message" => "Unable to create quote."));
                }
            } else {
                http_response_code(400); // Bad Request
                $message = "";
                if (!$authorExists) {
                    $message .= "author_id Not Found. ";
                }
                if (!$categoryExists) {
                    $message .= "category_id Not Found.";
                }
                echo json_encode(array("message" => $message));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Missing Required Parameters"));
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $this->quote->id = $data->id;
            $this->quote->quote = $data->quote;
            $this->quote->author_id = $data->author_id;
            $this->quote->category_id = $data->category_id;

            if ($this->quote->update()) {
                http_response_code(200); // OK
                echo json_encode(array("message" => "Quote was updated."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to update quote."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to update quote. Data is incomplete."));
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            $this->quote->id = $data->id;

            if ($this->quote->delete()) {
                http_response_code(200); // OK
                echo json_encode(array("message" => "Quote was deleted."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to delete quote."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to delete quote. Data is incomplete."));
        }
    }

    private function processGetResult($stmt){
        // ... (processGetResult code from previous responses)
    }
    
 // Helper functions to check if author and category exist
 private function authorExists($author_id) {
    $query = "SELECT id FROM authors WHERE id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $author_id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

private function categoryExists($category_id) {
    $query = "SELECT id FROM categories WHERE id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $category_id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
}
?>