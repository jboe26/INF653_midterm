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

        if (!is_array($result) || count($result) === 0) {
            http_response_code(404); // Not Found
            echo json_encode(["message" => "No Quotes Found"]);
            exit; // Ensure no further execution
        }

        http_response_code(200); // OK
        echo json_encode(["status" => "success", "data" => $result]);
        exit; // Ensure only one response is sent
    }

    public function handleRequest($method, $params) {
        switch ($method) {
            case 'GET':
                $this->handleGet($params);
                return; // Ensure script stops here
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
        if (isset($params[0])) { // Check if an ID is provided
            $this->quote->id = $params[0];
            $result = $this->quote->readOne();

            if ($result) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Quote Not Found."]);
            }
        } else {
            $this->fetchQuotes(); // Reuse the fetchQuotes method
        }
        exit; // Terminate script to prevent extra output
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $authorExists = $this->authorExists($data->author_id);
            $categoryExists = $this->categoryExists($data->category_id);

            if ($authorExists && $categoryExists) {
                $this->quote->quote = $data->quote;
                $this->quote->author_id = $data->author_id;
                $this->quote->category_id = $data->category_id;

                if ($this->quote->create()) {
                    http_response_code(201); // Created
                    echo json_encode(["message" => "Quote was created."]);
                    exit;
                } else {
                    http_response_code(503); // Service Unavailable
                    echo json_encode(["message" => "Unable to create quote."]);
                    exit;
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
                echo json_encode(["message" => $message]);
                exit;
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Missing Required Parameters"]);
            exit;
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
                echo json_encode(["message" => "Quote was updated."]);
                exit;
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to update quote."]);
                exit;
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to update quote. Data is incomplete."]);
            exit;
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            $this->quote->id = $data->id;

            if ($this->quote->delete()) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Quote was deleted."]);
                exit;
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to delete quote."]);
                exit;
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to delete quote. Data is incomplete."]);
            exit;
        }
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