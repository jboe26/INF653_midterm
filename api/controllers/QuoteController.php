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
                http_response_code(405);
                echo json_encode(['message' => 'Method not allowed']);
                exit;
        }
    }

    private function handleGet($params) {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $this->getQuoteById($_GET['id']);
        } elseif (isset($_GET['author_id']) && is_numeric($_GET['author_id']) && isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
            $this->getQuotesByAuthorAndCategory($_GET['author_id'], $_GET['category_id']);
        } elseif (isset($_GET['author_id']) && is_numeric($_GET['author_id'])) {
            $this->getQuotesByAuthor($_GET['author_id']);
        } elseif (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
            $this->getQuotesByCategory($_GET['category_id']);
        } else {
            $this->fetchQuotes();
        }
    }

    private function getQuoteById($id) {
        $this->quote->id = htmlspecialchars(strip_tags($id));
        $result = $this->quote->read_single();

        if ($result) {
            http_response_code(200);
            echo json_encode([
                "id" => $this->quote->id,
                "quote" => $this->quote->quote,
                "author" => $this->quote->author,
                "category" => $this->quote->category
            ]);
            exit;
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No Quotes Found"]);
            exit;
        }
    }

    private function getQuotesByAuthorAndCategory($author_id, $category_id) {
        $result = $this->quote->readByAuthorAndCategory($author_id, $category_id);
        $this->processQuoteResults($result, "No Quotes Found for this Author and Category.");
    }

    private function getQuotesByAuthor($author_id) {
        $result = $this->quote->readByAuthor($author_id);
        $this->processQuoteResults($result, "No Quotes Found for this Author.");
    }

    private function getQuotesByCategory($category_id) {
        $result = $this->quote->readByCategory($category_id);
        $this->processQuoteResults($result, "No Quotes Found for this Category.");
    }

    private function processQuoteResults($result, $errorMessage) {
        $quotes_arr = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $quotes_arr[] = [ // Corrected line: Append to the array
                "id" => $row["id"],
                "quote" => $row["quote"],
                "author" => $row["author"],
                "category" => $row["category"]
            ];
        }
    
        if (count($quotes_arr) > 0) {
            http_response_code(200);
            echo json_encode($quotes_arr);
            exit;
        } else {
            http_response_code(404);
            echo json_encode(["message" => $errorMessage]);
            exit;
        }
    }

    public function fetchQuotes() {
        $result = $this->quote->read();
        $this->processQuoteResults($result, "No Quotes Found");
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
                    http_response_code(201);
                    echo json_encode(["message" => "Quote was created"]);
                    exit;
                } else {
                    error_log("Failed to create quote: " . json_encode($data));
                    http_response_code(503);
                    echo json_encode(["message" => "Unable to create quote"]);
                    exit;
                }
            } else {
                http_response_code(400);
                $message = "";
                if (!$authorExists) $message .= "author_id Not Found";
                if (!$categoryExists) $message .= "category_id Not Found";
                echo json_encode(["message" => $message]);
                exit;
            }
        } else {
            http_response_code(400);
            $missingParams = [];
            if (empty($data->quote)) $missingParams= "quote";
            if (empty($data->author_id)) $missingParams= "author_id";
            if (empty($data->category_id)) $missingParams= "category_id";
            echo json_encode(["message" => "Missing required parameters: " . implode(", ", $missingParams)]);
            exit;
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
                http_response_code(200);
                echo json_encode(["message" => "Quote was updated"]);
                exit;
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to update quote"]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Unable to update quote. Data is incomplete"]);
            exit;
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            $this->quote->id = htmlspecialchars(strip_tags($data->id));

            if ($this->quote->delete()) {
                http_response_code(200);
                echo json_encode(["id" => $this->quote->id, "message" => "Quote was deleted"]);
                exit;
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to delete quote"]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Unable to delete quote. Data is incomplete"]);
            exit;
        }
    }

    private function authorExists($author_id) {
        if (!is_numeric($author_id)) return false;
        $query = "SELECT id FROM authors WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $author_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function categoryExists($category_id) {
        if (!is_numeric($category_id)) return false;
        $query = "SELECT id FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>