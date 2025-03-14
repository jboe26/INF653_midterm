<?php

include_once __DIR__ . '/../classes/Database.php';
include_once __DIR__ . '/../classes/Author.php';

class AuthorController {
    private $db;
    private $author;

    public function __construct($db) {
        $this->db = $db;
        $this->author = new Author($db);
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
        if (isset($params['id'])) { // Fetch a single author by ID
            if (!is_numeric($params['id'])) { // Validate ID
                http_response_code(400); // Bad Request
                echo json_encode(["message" => "Invalid or missing id parameter."]);
                return;
            }

            $this->author->id = htmlspecialchars(strip_tags($params['id']));
            $result = $this->author->readOne();

            if ($result) {
                http_response_code(200); // OK
                echo json_encode([
                    "id" => $this->author->id,
                    "author" => $this->author->author
                ]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "Author Not Found."]);
            }
        } else { // Fetch all authors
            $stmt = $this->author->read();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $authors_arr = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $authors_arr[] = [
                        "id" => $row['id'],
                        "author" => $row['author']
                    ];
                }
                http_response_code(200); // OK
                echo json_encode($authors_arr);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "No Authors Found."]);
            }
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->author)) { // Validate input
            $this->author->author = htmlspecialchars(strip_tags($data->author));

            if ($this->author->create()) {
                http_response_code(201); // Created
                echo json_encode(["message" => "Author was created."]);
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to create author."]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to create author. Data is incomplete."]);
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->author)) { // Validate input
            if (!is_numeric($data->id)) { // Validate ID
                http_response_code(400); // Bad Request
                echo json_encode(["message" => "Invalid id parameter."]);
                return;
            }

            $this->author->id = htmlspecialchars(strip_tags($data->id));
            $this->author->author = htmlspecialchars(strip_tags($data->author));

            if ($this->author->update()) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Author was updated."]);
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to update author."]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to update author. Data is incomplete."]);
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) { // Validate input
            if (!is_numeric($data->id)) { // Validate ID
                http_response_code(400); // Bad Request
                echo json_encode(["message" => "Invalid id parameter."]);
                return;
            }

            $this->author->id = htmlspecialchars(strip_tags($data->id));

            if ($this->author->delete()) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Author was deleted."]);
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(["message" => "Unable to delete author."]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Unable to delete author. Data is incomplete."]);
        }
    }
}
?>
