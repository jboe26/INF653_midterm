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
                http_response_code(405);
                echo json_encode(['message' => 'Method not allowed.']);
                exit;
        }
    }

    private function handleGet($params) {
        if (isset($params['id'])) {
            if (!is_numeric($params['id'])) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid or missing id parameter."]);
                exit;
            }

            $this->author->id = htmlspecialchars($params['id']);
            $result = $this->author->readOne();

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "id" => $this->author->id,
                    "author" => $this->author->author
                ]);
                exit;
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Author Not Found."]);
                exit;
            }
        } else {
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
                http_response_code(200);
                echo json_encode($authors_arr);
                exit;
            } else {
                http_response_code(404);
                echo json_encode(["message" => "No Authors Found"]);
                exit;
            }
        }
    }

    private function handlePost() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->author) && strlen($data->author) <= 255) {
            $this->author->author = htmlspecialchars(strip_tags($data->author));

            if ($this->author->create()) {
                http_response_code(201);
                echo json_encode([
                    "id" => $this->author->id,
                    "author" => $this->author->author,
                    "message" => "Author was created"
                ]);
                exit;
            } else {
                error_log("Failed to create author: " . json_encode($data));
                http_response_code(503);
                echo json_encode(["message" => "Unable to create author"]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Author name is either empty or too long"]);
            exit;
        }
    }

    private function handlePut() {
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id;

        if (!empty($id) && !empty($data->author) && strlen($data->author) <= 255) {
            if (!is_numeric($id)) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid id parameter"]);
                exit;
            }

            $this->author->id = htmlspecialchars($id);
            $this->author->author = htmlspecialchars(strip_tags($data->author));

            if ($this->author->update()) {
                http_response_code(200);
                echo json_encode([
                    "id" => $this->author->id,
                    "author" => $this->author->author,
                    "message" => "Author was updated"
                ]);
                exit;
            } else {
                error_log("Failed to update author with ID: " . $id);
                http_response_code(503);
                echo json_encode(["message" => "Unable to update author"]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Author name is either empty or too long"]);
            exit;
        }
    }

    private function handleDelete() {
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id;

        if (!empty($id)) {
            if (!is_numeric($id)) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid id parameter"]);
                exit;
            }

            $this->author->id = htmlspecialchars($id);

            if ($this->author->delete()) {
                http_response_code(200);
                echo json_encode(["id" => $this->author->id, "message" => "Author was deleted"]);
                exit;
            } else {
                error_log("Failed to delete author with ID: " . $id);
                http_response_code(503);
                echo json_encode(["message" => "Unable to delete author"]);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Unable to delete author. Data is incomplete"]);
            exit;
        }
    }
}
?>